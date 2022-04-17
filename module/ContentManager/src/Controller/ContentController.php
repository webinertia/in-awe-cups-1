<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use Application\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Webinertia\ModelManager\ModelManager;

final class ContentController extends AbstractController
{
    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    public function pageAction(): ViewModel
    {
        $params = $this->params()->fromRoute();
        $this->view->setVariable('params', $params);
        return $this->view;
    }
}
