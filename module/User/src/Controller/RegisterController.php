<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Service\Email;
use DateTime;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use User\Filter\RegistrationHash as Filter;
use User\Form\UserForm;
use User\Service\UserInterface;

use function array_merge;
use function password_verify;
use function strpos;
use function substr;

class RegisterController extends AbstractAppController
{
    /** @var UserForm $form */
    protected $form;
    /** @var string $resourceId */
    protected $resourceId = 'account';
    /**
     * The default action - show the action
     */
    public function indexAction(): object
    {
        $this->form  = $this->service()->get(FormElementManager::class)->get(UserForm::class);
        $appSettings = $this->service('config')['app_settings'];
        // if registration is disabled return as there is nothing more to do
        if (! $appSettings['security']['enable_registration']) {
            return $this->view;
        }
        $mailService = $this->service(Email::class);
        // we need a timestamp based on the server settings
        $now = new DateTime();
        // time format is 02/13/1975
        $timeStamp = $now->format($appSettings['server']['time_format']);
        if (! $this->request->isPost()) {
            $data['acct-data']['regDate'] = $timeStamp;
            $this->form->setData($data);
            // Initial page load, send them the form
            return $this->view->setVariable('form', $this->form);
        }
        $this->form->setData($this->request->getPost());
        if (! $this->form->isValid()) {
            $this->view->setVariable('form', $this->form);
            return $this->view;
        }
        // get  the valid data from the form, we need to add to it before user is saved
        $acctData     = $this->form->getData()['acct-data'];
        $profileData  = $this->form->getData()['profile-data'];
        $passwordData = $this->form->getData()['password-data'];
        $roleData     = $this->form->getData()['role-data'] ??= ['role' => 'Member'];
        unset($passwordData['conf_password']);
        $value     = ['email' => $acctData['email'], 'timestamp' => $acctData['regDate']];
        $filter    = new Filter();
        $hash      = $filter->filter($value);
        $token     = $acctData['email'] . $hash;
        $result    = $this->usrGateway->insert(
            array_merge($acctData, $profileData, $passwordData, $roleData, ['regHash' => $hash])
        );
        $sendEmail = false;
        if ($result > 0) {
            $sendEmail = true;
        }
        if ($sendEmail) {
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
            $user     = $this->usrGateway->fetchByColumn('email', $email);
            if ($user instanceof UserInterface) {
                $check = password_verify($email . $user->regDate, $user->regHash);
                if ($check) {
                    $user->active   = 1;
                    $user->verified = 1;
                    $user->regHash  = null;
                    $result         = $this->usrGateway->update($user, ['id' => $user->id]);
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
