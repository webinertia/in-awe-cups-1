<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Laminas\Authentication\Result;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Filter\FormFilters;
use User\Form\EditUserForm;
use User\Form\LoginForm;
use User\Model\Roles;
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

    public function editAction(): ViewModel
    {
        try {
            $logout = false;
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            // this is the proper fetch for a user, all other calls are to be removed
            $user = $this->usrModel->fetchByColumn('userName', $userName);
            // if they can not edit the user there is no point in preceeding
            if (! $this->acl->isAllowed($this->user, $user, $this->action)) {
                $this->flashMessenger()->addWarningMessage('You do not have the required permissions to edit users');
                $this->redirect()->toRoute('home');
            } else {
                $options               = [];
                $options['acl']        = $this->acl;
                $options['settings']   = $this->appSettings;
                $options['rolesTable'] = $this->sm->get(Roles::class);
                $options['user']       = $this->user;
                $form                  = new EditUserForm(null, $options);
                $form->get('submit')->setAttribute('value', 'Edit');
                // if this is not a post lets return early
                $viewData['userName'] = $user->userName;
                if (! $this->request->isPost()) {
                // bind the queried user data to the form
                    $form->bind($user);
                    // we should only need this when its not post, when form is initially built
                    $viewData['form'] = $form;
                    $this->view->setVariables($viewData);
                    return $this->view;
                }
                $filters = new FormFilters();
                // Set the input filters in the form object
                $form->setInputFilter($filters->getEditUserFilter($this->usrModel, $user->id));
                // get the posted data
                $post = $this->request->getPost();
                // Set the posted data in the form so that it can be validated
                $form->setData($this->request->getPost());
                // Validate the posted data via the filters set in the form object
                // TODO: Fix this, this form object has no filters or validators defined in the form class
                if (! $form->isValid()) {
                    $this->view->form = $form;
                    return $this->view;
                }
                // testing
                $validatedData = $form->getData();
                // ok so just because we have a valid password doesnt mean we want to use it here
                if ($post['password'] === '') {
                /**
                 * In this condition using the validatedData['password'] means its a passwordhash'ed empty string
                 * the user will no longer be able to login we cant use it
                 */
                    unset($validatedData['password']);
                }
                $result = $this->usrModel->insert($validatedData, true);
                if ($result) {
                // Redirect to User list
                    return $this->redirect()->toRoute('user', ['action' => 'index']);
                } else {
                    throw new RuntimeException('The user could not be updated at this time');
                }
            }
        } catch (RuntimeException $e) {
        }
    }

    public function deleteAction(): object
    {
        // verify that the session cleared during user deletion
        try {
            $userName    = $this->params()->fromRoute('userName');
            $user        = $this->usrModel->fetchByColumn('userName', $userName);
            $deletedUser = $user->toArray();
            if ($this->acl->isAllowed($this->user, $user, $this->action)) {
                $result = $user->delete();
                if ($result > 0) {
                    $this->logger->info(
                        'User ' . $this->user->userName . ' deleted user: ' . $deletedUser['userName'],
                        [
                            'userId'   => $this->user->id,
                            'userName' => $this->user->userName,
                            'role'     => $this->user->role,
                        ]
                    );
                    return $this->redirect()->toRoute(
                        'user',
                        ['action' => 'index', 'userName' => $deletedUser['userName']]
                    );
                } else {
                    throw new RuntimeException('The requested action could not be completed');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Forbidden action');
            }
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
