<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractController;
use Laminas\Authentication\Result;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\LoginForm;
use User\Model\Users;

class UserController extends AbstractController
{
    /** @var Users $usrModel */
    public $usrModel;
    /** @return void */
    public function __construct(Users $usrModel)
    {
        // comment for test commit
        $this->usrModel = $usrModel;
    }

    public function listAction(): ViewModel
    {
        try {
           // $view       = new ViewModel();
            $userName   = $this->params('userName');
            $hasMessage = false;
            if (! empty($userName)) {
                $this->fm = $this->plugin('flashMessenger');
                $this->fm->addSuccessMessage('User ' . $userName . ' was successfully deleted!!');
                $hasMessage = true;
            }
            $this->view->setVariable('hasMessage', $hasMessage);
            $this->view->setVariable('users', $this->usrModel->fetchAll());
            return $this->view;
        } catch (RuntimeException $e) {
        }
    }

    public function forgotPasswordAction()
    {
    }

    public function logoutAction(): object
    {
        switch ($this->authService->hasIdentity()) {
            case true:
                $this->authService->clearIdentity();
                return $this->redirect()->toRoute('home');
            break;
            case false:
                break;
            default:
                break;
        }
    }

    public function loginAction(): object
    {
        $form = ($this->sm->get(FormElementManager::class))->get(LoginForm::class);
        if (! $this->request->isPost()) {
            $this->view->setVariable('form', $form);
            return $this->view;
        }
        // set the posted data in the form objects context
        $form->setData($this->request->getPost());
        // check with the form object to verify data is valid
        if (! $form->isValid()) {
            $this->view->setVariable('form', $form);
            return $this->view;
        }
        // we should have valid data that is filtered and validated by this point
        $this->usrModel->exchangeArray($form->getData()['login-data']);
        $loginResult = $this->usrModel->login($this->usrModel);
        if ($loginResult->isValid()) {
            $this->usrModel->exchangeArray($this->usrModel->fetchByColumn('userName', $loginResult->getIdentity()));
            $this->flashMessenger()->addInfoMessage('Welcome back!!');
            return $this->redirect()->toRoute('user/profile', ['userName' => $this->usrModel->userName]);
        } else {
            $messages = $loginResult->getMessages();
            switch ($loginResult->getCode()) {
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    $fieldset   = $form->get('login-data');
                    $element    = $fieldset->get('userName');
                    $messages[] = 'If you are certain you have registered you may need to verify your account before you can login';
                    $element->setMessages($messages);
                    break;
                case Result::FAILURE_CREDENTIAL_INVALID:
                    $fieldset = $form->get('login-data');
                    $element  = $fieldset->get('password');
                    $element  = $fieldset->get('password');
                    $element->setMessages($messages);
                    break;
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
