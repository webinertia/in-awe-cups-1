<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\ViewModel;

final class ContentController extends AbstractAppController
{
    /** @var string $resourceId */
    protected $resourceId = 'pages';

    public function pageAction(): ViewModel
    {
        $navigation = $this->service()->get(Navigation::class);
        $title      = $this->params('title');
        if (! $navigation->findOneBy('title', $title)) {
            $this->view->setVariable('message', 'The requested page could not be found.');
            $this->response->setStatusCode('404');
        }
        $this->view->setVariables([
            'title' => $title,
        ]);
        $this->layout()->setVariable('renderPage', true);
        return $this->view;
    }
}
