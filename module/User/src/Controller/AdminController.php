<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAdminController;
use Laminas\Log\Logger;
use Laminas\View\Model\ViewModel;
use Throwable;
use User\Model\Users as UsrModel;

final class AdminController extends AbstractAdminController
{
    /** @var User\Model\Users $usrModel */
    protected $usrModel;
    /** @return void */
    public function __construct(UsrModel $usrModel)
    {
        $this->usrModel = $usrModel;
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
            $this->view->setVariable('users', $this->usrModel->loadMemberContext());
            return $this->view;
        } catch (Throwable $th) {
            $this->logger->log(Logger::ERR, $th->getMessage());
        }
    }
}
