<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\ViewModel;

final class LogController extends AbstractAppController implements AdminControllerInterface
{
    protected $resourceId = 'admin';
    public function overViewAction(): ViewModel
    {
        return $this->view;
    }

    public function errorAction()
    {
        return $this->view;
    }
}
