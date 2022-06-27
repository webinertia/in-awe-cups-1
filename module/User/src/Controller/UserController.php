<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use Laminas\Authentication\Result;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\LoginForm;

final class UserController extends AbstractAppController
{

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

    public function forgotPasswordAction()
    {
    }
}
