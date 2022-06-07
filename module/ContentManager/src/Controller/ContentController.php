<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;

use function count;

final class ContentController extends AbstractAppController
{
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
