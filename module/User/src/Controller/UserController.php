<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Model\ViewModel;
use RuntimeException;

final class UserController extends AbstractAppController implements ResourceInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'member-list';

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
            $this->view->setVariable('users', $this->usrGateway->fetchAll());
            return $this->view;
        } catch (RuntimeException $e) {
        }
    }

    public function forgotPasswordAction()
    {
    }
}
