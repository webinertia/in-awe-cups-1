<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Application\Model\Settings;
use Application\Service\Email;
use DateTime;
use Laminas\View\Model\ViewModel;
use User\Filter\RegistrationHash as Filter;
use User\Form\UserForm;
use User\Model\Users;

use function array_merge;
use function password_verify;
use function strpos;
use function substr;

class RegisterController extends AbstractController
{
    /** @var Users */
    protected $usrModel;
    /**
     * @return void
     */
    public function __construct(Users $usrModel, UserForm $form, Settings $appSettings)
    {
        $this->usrModel    = $usrModel;
        $this->form        = $form;
        $this->appSettings = $appSettings;
    }

    /**
     * The default action - show the action
     */
    public function indexAction(): object
    {
        // if registration is disabled return as there is nothing more to do
        if (! $this->appSettings->security->enable_registration) {
            return $this->view;
        }
        $mailService = $this->sm->get(Email::class);
        // we need a timestamp based on the server settings
        $now = new DateTime();
        // time format is 02/13/1975
        $timeStamp = $now->format($this->appSettings->server->time_format);
        if (! $this->request->isPost()) {
            $data['acct-data']['regDate'] = $timeStamp;
            $this->form->setData($data);
            // Initial page load, send them the form
            return $this->view->setVariable('form', $this->form);
        }
        // if weve made it to here then its a post request
        $post = $this->request->getPost();
        //$this->form->setInputFilter($this->formFilters->getInputFilter());
        $this->form->setData($this->request->getPost());
        // Is the posted form data valid? if not send them the form back and the problems
        // reported by the filters and validators
        if (! $this->form->isValid()) {
            $this->view->setVariable('form', $this->form);
            return $this->view;
        }

        // get  the valid data from the form, we need to add to it before user is saved
        $acctData     = $this->form->getData()['acct-data'];
        $profileData  = $this->form->getData()['profile-data'];
        $passwordData = $this->form->getData()['password-data'];
        $roleData     = $this->form->getData()['role-data'] ??= 'user';
        unset($passwordData['conf_password']);
        $value  = ['email' => $acctData['email'], 'timestamp' => $acctData['regDate']];
        $filter = new Filter();
        $hash   = $filter->filter($value);
        $token  = $acctData['email'] . $hash;
        // save the new user, $result should be the new users Id
        $this->usrModel->exchangeArray(
            array_merge($acctData, $profileData, $passwordData, $roleData, ['regHash' => $hash])
        );
        $result    = $this->usrModel->insert($this->usrModel);
        $sendEmail = false;
        if ($result > 0) {
            $sendEmail = true;
        }
        if ($sendEmail) {
            $this->hostName      = $this->request->getServer('HTTP_HOST');
            $this->requestScheme = $this->request->getServer('REQUEST_SCHEME');
            $mailService->sendMessage($acctData['email'], Email::VERIFICATION, $token);
        }
        return $this->redirect()->toRoute('home');
    }

    public function verifyAction(): ViewModel
    {
        $token = $this->request->getQuery('token');
        if (! empty($token)) {
            $position = strpos($token, '$');
            $email    = substr($token, 0, $position);
            $user     = $this->usrModel->fetchByColumn('email', $email);
            if ($user instanceof Users) {
                $check = password_verify($email . $user->regDate, $user->regHash);
                if ($check) {
                    $user->active   = 1;
                    $user->verified = 1;
                    $user->regHash  = null;
                    $result         = $user->update($user, ['id' => $user->id]);
                    if ($result) {
                        $this->view->setVariable('verified', true);
                    } else {
                        $this->view->setVariable('verified', false);
                    }
                }
            }
        }
        return $this->view;
    }
}
