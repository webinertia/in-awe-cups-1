<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\ProfileForm;

final class ProfileController extends AbstractAppController
{
    /** @var Users $usrModel */
    protected $usrModel;
    /** @var ProfileForm $form */
    protected $form;
    /** @var string $resourceId */
    protected $resourceId = 'profile';

    /**
     * @return mixed
     * @throws DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        if (! $this->identity()->hasIdentity()) {
            $this->redirect()->toRoute('user/account/login');
        }
        return parent::onDispatch($e);
    }

    public function viewAction(): ViewModel
    {
        try {
            $user     = $this->identity()->getIdentity();
            $userName = $this->params()->fromRoute('userName', $user->userName);
            if ($userName === $user->userName) {
                $profileData = $user;
            } else {
                $profileData = $this->usrGateway->fetchByColumn('userName', $userName);
            }
            $this->view->setVariable('data', $profileData);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            $this->view->setVariables(['message' => 'User not found', 'reason' => null]);
            $this->response->setStatusCode(404);
        }
        return $this->view;
    }
}
