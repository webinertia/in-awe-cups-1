<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Product;

class AdminController extends AbstractAppController implements AdminControllerInterface
{
    public function overviewAction(): ModelInterface
    {
        $this->view->setTerminal(true);

        return $this->view;
    }

    public function indexAction(): ModelInterface
    {
        return new ViewModel();
    }

    public function getResourceId(): string
    {
        return 'admin';
    }
}
