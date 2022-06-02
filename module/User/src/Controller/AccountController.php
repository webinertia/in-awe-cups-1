<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractController;
use App\Form\FormInterface;
use App\Model\Settings;
use Laminas\Form\FormElementManager;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;
use User\Form\UserForm;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

use function array_merge;

final class AccountController extends AbstractController
{
    /**
     * @return void
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function __construct(ModelManager $modelManager, FormElementManager $formManager)
    {
        $this->modelManager = $modelManager;
        $this->formManager  = $formManager;
        $this->usrModel     = $this->modelManager->get(Users::class);
        $this->appSettings  = $this->modelManager->get(Settings::class);
    }

    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }

    /** @return mixed */
    public function editAction()
    {
        try {
            $form = $this->formManager->build(UserForm::class, ['mode' => FormInterface::EDIT_MODE]);
            // get the user by userName that is to be edited
            $userName = $this->params()->fromRoute('userName');
            // if they can not edit the user there is no point in preceeding
            // fetch the user data
            $user = $this->usrModel->fetchByColumn('userName', $userName);
            if (! $this->acl->isAllowed($this->user, $user, $this->action)) {
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
                    return $this->redirect()->toRoute('user/list', ['page' => 1, 'count' => 5]);
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
}
