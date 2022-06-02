<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractController;
use Laminas\View\Model\ViewModel;
use Webinertia\ModelManager\ModelManager;

use function count;

final class ContentController extends AbstractController
{
    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    public function pageAction(): ViewModel
    {
        $hasParent = false;
        $params    = $this->params()->fromRoute();
        if (count($params) > 3) {
            $hasParent = true;
        } else {
            $params['title'] = $params['parentTitle'];
        }
        $this->view->setVariables([
            'params'    => $params,
            'hasParent' => $hasParent,
        ]);
        return $this->view;
    }
}
