<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use Laminas\Authentication\Result;
use Laminas\Form\FormElementManager;
use Laminas\Log\Logger;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Form\LoginForm;
use User\Form\UserForm;

use function array_merge;

final class AccountController extends AbstractAppController
{
    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }

    public function loginAction(): ViewModel
    {
        $formManager = $this->service()->get(FormElementManager::class);
        $form        = $formManager->get(LoginForm::class);
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
        $loginData   = $form->getData()['login-data'];
        $loginResult = $this->usrModel->login($loginData['userName'], $loginData['password']);
        if ($loginResult->isValid()) {
            $userInterface = $this->usrModel->fetchByColumn('userName', $loginResult->getIdentity());
            $this->flashMessenger()->addInfoMessage('Welcome back!!');
            return $this->redirect()->toRoute('user/profile', ['userName' => $userInterface->userName]);
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
    public function editAction(): ViewModel
    {
        try {
            $this->resourceId = 'account';
            $form             = $this->formManager->build(UserForm::class, ['mode' => FormInterface::EDIT_MODE]);
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            // if they can not edit the user there is no point in preceeding
            // fetch the user data
            $user = $this->usrModel->fetchByColumn('userName', $userName);
            if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $user, $this->action)) {
                $this->flashMessenger()->addWarningMessage('You do not have the required permissions to edit users');
                $this->redirect()->toRoute('home');
            }
            if (! $this->request->isPost()) {
                $data = [
                    'acct-data'    => [
                        'id'    => $user->id,
                        'email' => $user->email,
                    ],
                    'role-data'    => [
                        'role' => $user->role,
                    ],
                    'profile-data' => [
                        'firstName' => $user->firstName,
                        'lastName'  => $user->lastName,
                        'age'       => $user->age,
                        'birthday'  => $user->birthday,
                        'bio'       => $user->bio,
                    ],
                ];
                $form->setData($data);
            } else {
                $data = $this->request->getPost();
                $form->setData($data);
                if (! $form->isValid()) {
                    $this->view->setVariable('form', $form);
                    return $this->view;
                } else {
                    $valid = $form->getData();
                    $user->exchangeArray(array_merge($valid['acct-data'], $valid['role-data'], $valid['profile-data']));
                    $result = $this->usrModel->update($user);
                }

                if ($result) {
                    // Redirect to User list
                    $this->redirect()->toRoute('user/list', ['page' => 1, 'count' => 5]);
                } else {
                    throw new RuntimeException('The user could not be updated at this time');
                }
            }
            $this->view->setVariable('form', $form);
            return $this->view;
        } catch (Throwable $th) {
            $this->logger->log(Logger::ERR, $th->getMessage());
        }
    }

    public function deleteAction(): void
    {
        // verify that the session cleared during user deletion
        try {
            $userName    = $this->params()->fromRoute('userName');
            $user        = $this->usrModel->fetchByColumn('userName', $userName);
            $deletedUser = $user->toArray();
            if ($this->isAllowed($this->user, $user, $this->action)) {
                $result = $user->delete();
                if ($result > 0) {
                    $this->logger->info(
                        'User ' . $this->user->userName . ' deleted user: ' . $deletedUser['userName'],
                        $this->user->getLogData()
                    );
                    $this->redirect()->toRoute(
                        'user',
                        ['action' => 'index', 'userName' => $deletedUser['userName']]
                    );
                } else {
                    throw new RuntimeException('The requested action could not be completed');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Forbidden action');
                $this->response->setStatusCode('403');
                $this->redirectPrev();
            }
        } catch (RuntimeException $e) {
            $this->logger->log(Logger::ERR, $e->getMessage(), $this->user->getLogData());
        }
    }

    public function logoutAction(): object
    {
        switch ($this->identity()->hasIdentity()) {
            case true:
                $this->identity()->clearIdentity();
                return $this->redirect()->toRoute('home');
            break;
            case false:
                break;
            default:
                break;
        }
    }
}
