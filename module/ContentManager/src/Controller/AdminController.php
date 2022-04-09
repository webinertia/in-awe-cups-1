<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use Application\Controller\AbstractAdminController;
use ContentManager\Model\Page;
use ContentManager\Model\Pages;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use Webinertia\ModelManager\ModelManager;

class AdminController extends AbstractAdminController
{
    /** @var Page $page */
    /** @var Pages $pages */
    public function __construct(ModelManager $modelManager, FormElementManager $formElementManager)
    {
    }

    public function init(): self
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        return $this;
    }

    public function createAction(): ViewModel
    {
        return $this->view;
    }

    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }

    public function updateAction(): ViewModel
    {
        return $this->view;
    }
}
