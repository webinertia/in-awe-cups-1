<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use ContentManager\Model\Page;
use Laminas\Navigation\Navigation;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\View\Model\ViewModel;

final class ContentController extends AbstractAppController
{
    /** @var string $resourceId */
    protected $resourceId = 'pages';

    public function pageAction(): ViewModel
    {
        $navigation = $this->getService(Navigation::class);
        $title      = $this->params('title');
        $page       = $navigation->findOneBy('title', $title);
        if ($page === null) {
            $model = $this->getService(Page::class);
            $page  = $model->fetchByColumn('title', $title);
        }
        if (! $page instanceof AbstractPage && ! $page instanceof Page) {
            $this->view->setVariable('message', 'The requested page could not be found.');
            $this->response->setStatusCode(404);
        }
        $this->view->setVariables(
            [
                'title' => $title,
                'page'  => $page,
            ]
        );
        $this->layout()->setVariable('renderPage', true);
        return $this->view;
    }
}
