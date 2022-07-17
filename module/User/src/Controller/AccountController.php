<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use Laminas\Authentication\Result;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Acl\CheckActionAccessTrait;
use User\Form\LoginForm;
use User\Form\UserForm;

use function array_merge;

final class AccountController extends AbstractAppController
{
    use CheckActionAccessTrait;

    /** @var string $resourceId */
    protected $resourceId = 'account';

    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }

    public function loginAction(): mixed
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
        $loginResult = $this->usrGateway->login($loginData['userName'], $loginData['password']);
        if ($loginResult->isValid()) {
            $userInterface = $this->usrGateway->fetchByColumn('userName', $loginResult->getIdentity());
            $this->flashMessenger()->addInfoMessage('Welcome back!!');
            return $this->redirect()->toRoute('user/profile', ['userName' => $userInterface->userName]);
        } else {
            $messages = $loginResult->getMessages();
            switch ($loginResult->getCode()) {
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    $fieldset   = $form->get('login-data');
                    $element    = $fieldset->get('userName');
                    $messages[] = 'If you have registered, please check your email for the activation link.';
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
            $formManager = $this->service()->get(FormElementManager::class);
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            $user     = $this->usrGateway->fetchByColumn('userName', $userName);
            if (! $this->isAllowed()) {
                $this->flashMessenger()->addWarningMessage('You do not have the required permissions to edit users');
                $this->redirect()->toRoute('home');
            }
            $form     = $formManager->build(
                UserForm::class,
                ['mode' => FormInterface::EDIT_MODE, 'userId' => $user->id]
            );
            $userName = $form->get('acct-data')->get('userName');
            $userName->setAttribute('readonly', 'readonly');
            if (! $this->request->isPost()) {
                $data = [
                    'acct-data'    => [
                        'id'       => $user->id,
                        'userName' => $user->userName,
                        'email'    => $user->email,
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
                if (! $form->isValid()) { // if the form does not validate, check the acct-data fieldset filter
                    $this->view->setVariable('form', $form);
                    return $this->view;
                } else {
                    $valid  = $form->getData();
                    $result = $this->usrGateway->update(
                        array_merge($valid['acct-data'], $valid['role-data'], $valid['profile-data']),
                        ['id' => $valid['acct-data']['id']]
                    );
                }
                if ($result) {
                    // Redirect to User list
                    $this->redirect()->toRoute('user/list', ['page' => 1, 'count' => 5]);
                } else {
                    throw new RuntimeException('The user could not be updated at this time');
                }
            }
            $this->view->setVariable('form', $form);
        } catch (Throwable $th) {
            $this->getLogger()->error($th->getMessage());
        }
        return $this->view;
    }

    public function deleteAction(): void
    {
        // verify that the session cleared during user deletion
        try {
            $userName    = $this->params()->fromRoute('userName');
            $user        = $this->usrGateway->fetchByColumn('userName', $userName);
            $deletedUser = $user->toArray();
            if ($this->isAllowed($this->user, $user, $this->action)) {
                $result = $this->usrGateway->delete(['userName' => $user->userName]);
                if ($result > 0) {
                    $this->getLogger()->info(
                        'User ' . $deletedUser['firstName'] . ' ' . $deletedUser['lastName'] . ' was deleted'
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
            $this->getLogger()->error($e->getMessage());
        }
    }

    public function logoutAction(): object
    {
        if ($this->identity()->hasIdentity()) {
            $this->identity()->clearIdentity();
            $this->flashMessenger()->addInfoMessage('You have been successfully logged out');
            return $this->redirect()->toRoute('home');
        } else {
            $this->flashMessenger()->addErrorMessage(
                'An unknown error occurred please contact the system administrator'
            );
            $this->getLogger()->critical('System failed to log the user out!!');
            return $this->redirect()->toRoute('home');
        }
    }

    public function staffActivateAction(): ViewModel
    {
        try {
            $this->user = $this->identity()->getIdentity();
            if ($this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
                if ($this->request->isXmlHttpRequest()) {
                    $this->view->setTerminal(true);
                }
                $userName     = $this->params()->fromRoute('userName');
                $user         = $this->usrGateway->fetchByColumn('userName', $userName);
                $user->active = 1;
                $result       = $this->usrGateway->update($user->toArray(), ['id' => $user->id]);
                if ($result) {
                    $this->getLogger()->info(
                        'User with UserName: ' . $user->userName . ' has been activated by staff member'
                    );
                } else {
                    throw new RuntimeException('The requested action could not be completed');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Forbidden action');
                $this->response->setStatusCode('403');
            }
        } catch (RuntimeException $e) {
            $this->getLogger()->error($e->getMessage());
        }
        $this->view->setVariables(['user' => $this->user, 'activatedUser' => $user]);
        return $this->view;
    }

    public function staffDeactivateAction(): ViewModel
    {
        try {
            $this->user = $this->identity()->getIdentity();
            if ($this->acl()->isAllowed($this->identity()->getIdentity(), 'account', 'create')) {
                if ($this->request->isXmlHttpRequest()) {
                    $this->view->setTerminal(true);
                }
                $userName     = $this->params()->fromRoute('userName');
                $user         = $this->usrGateway->fetchByColumn('userName', $userName);
                $user->active = 0;
                $result       = $this->usrGateway->update($user->toArray(), ['id' => $user->id]);
                if ($result) {
                    $this->getLogger()->info(
                        'User with UserName: ' . $user->userName . ' has been deactivated by staff member'
                    );
                } else {
                    throw new RuntimeException('The requested action could not be completed');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Forbidden action');
                $this->response->setStatusCode('403');
            }
        } catch (RuntimeException $e) {
            $this->getLogger()->error($e->getMessage());
        }
        $this->view->setVariables(['user' => $this->user, 'deactivatedUser' => $user]);
        return $this->view;
    }
}
