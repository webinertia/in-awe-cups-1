<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Log\LogEvent;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\ProfileForm;

use function sprintf;

final class ProfileController extends AbstractAppController
{
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
                $profileData = $this->userService->fetchByColumn('userName', $userName);
            }
            $this->view->setVariable('data', $profileData);
        } catch (RuntimeException $e) {
            $message = $this->getTranslator()->translate('log_profile_not_found_404');
            $this->getEventManager()->trigger(
                LogEvent::ERROR,
                $message
                . ': ' . $e->getFile() . ':' . $e->getLine() . ': ' . $e->getMessage()
            );
            $this->view->setVariables(
                [
                    'message' => sprintf(
                        $this->getTranslator()->translate('profile_known_user_not_found_404'),
                        $userName
                    ),
                ]
            );
            $this->response->setStatusCode(404);
        }
        return $this->view;
    }
}
