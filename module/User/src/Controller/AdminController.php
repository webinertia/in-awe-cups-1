<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\Log\Logger;
use Laminas\View\Model\ViewModel;
use Throwable;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function indexAction(): ViewModel
    {
        return $this->view;
    }

    public function widgetAction(): ViewModel
    {
        try {
            if ($this->request->isXmlHttpRequest()) {
                $this->view->setTerminal(true);
            }
            $userName   = $this->params('userName');
            $hasMessage = false;
            if (! empty($userName)) {
                $this->fm = $this->plugin('flashMessenger');
                $this->fm->addSuccessMessage('User ' . $userName . ' was successfully deleted!!');
                $hasMessage = true;
            }
            $this->view->setVariable('users', $this->usrGateway->loadMemberContext());
            return $this->view;
        } catch (Throwable $th) {
            $this->logger->log(Logger::ERR, $th->getMessage());
        }
    }
}
