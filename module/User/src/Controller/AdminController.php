<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractAdminController;
use RuntimeException;
use User\Model\Users as UsrModel;

class AdminController extends AbstractAdminController
{
    /**
     * @var User\Model\Users $usrModel
     */
    /**
     * @return void
     */
    public function __construct(UsrModel $usrModel)
    {
        $this->usrModel = $usrModel;
    }

    public function indexAction()
    {
        //var_dump($this->user);
    }

    public function widgetAction()
    {
        //$this->view = new JsonModel();
        try {
            $userName   = $this->params('userName');
            $hasMessage = false;
            if (! empty($userName)) {
                $this->fm = $this->plugin('flashMessenger');
                $this->fm->addSuccessMessage('User ' . $userName . ' was successfully deleted!!');
                $hasMessage = true;
            }
           /// $this->view->setVariable('hasMessage', $hasMessage);
            $this->view->setVariable('users', $this->usrModel->loadMemberContext());
            return $this->view;
        } catch (RuntimeException $e) {
        }
    }
}
