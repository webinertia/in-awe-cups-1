<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\FormInterface;
use ContentManager\Form\PageForm;
use ContentManager\Model\Page;
use ContentManager\Model\Pages;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Json\Encoder;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var Page $page */
    /** @var Pages $pages */
    /** @var PageForm $form */
    /**
     * @param ContainerInterface $container
     * @return AdminController
     * @throws ServiceNotFoundException
     * @throws InvalidServiceException
     */
    public function init($container): self
    {
        $this->pages = $this->modelManager->get(Pages::class);
        return $this;
    }

    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function createAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->formManager->build(PageForm::class, ['mode' => FormInterface::CREATE_MODE]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager', ['action' => 'create'])
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $filter       = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data         = $form->getData();
                $data->userId = $this->user->id;
                $data->title  = $filter->filter($data->label);
                // need to add a parentTitle column to the table so that it can be injected into the select
                if (! isset($data->parentId)) {
                    $data->route  = 'content/category';
                    $data->params = Encoder::encode(['parentTitle' => $data->title]);
                }
                $result = $data->save();
                try {
                    if (! $result) {
                        throw new RuntimeException('Page Not saved');
                    }
                } catch (RuntimeException $e) {
                    $this->logger->log(Logger::EMERG, $e->getMessage(), $this->user->getLogData());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function dashboardAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        return $this->view;
    }

    public function updateAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        return $this->view;
    }
}
