<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use ContentManager\Form\PageForm;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ContentController extends AbstractAppController
{
    /** @var Navigation $navigation */
    protected $navigation;
    /**
     * @param ContainerInterface $container
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function init($container): AbstractAppController
    {
        $this->navigation = $container->get(Navigation::class);
        return $this;
    }

    public function pageAction(): ViewModel
    {
        $title = $this->params('title');
        if (! $this->navigation->findOneBy('title', $title)) {
            $this->view->setVariable('message', 'The requested page could not be found.');
            $this->response->setStatusCode('404');
        }
        $form = $this->formManager->build(PageForm::class, ['mode' => FormInterface::CREATE_MODE]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager', ['action' => 'create'])
        );
        $this->view->setVariables([
            'title' => $title,
            'form'  => $form,
        ]);
        $this->layout()->setVariable('renderPage', true);
        return $this->view;
    }
}
