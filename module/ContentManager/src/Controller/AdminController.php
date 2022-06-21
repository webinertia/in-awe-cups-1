<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\FormInterface;
use ContentManager\Form\PageForm;
use ContentManager\Model\Page;
use ContentManager\Model\Pages;
use Laminas\Filter\Exception\InvalidArgumentException as FilterExceptionInvalidArgumentException;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Http\Header\Exception\RuntimeException as ExceptionRuntimeException;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Json\Encoder;
use Laminas\Json\Exception\RecursionException;
use Laminas\Log\Exception\InvalidArgumentException as LogExceptionInvalidArgumentException;
use Laminas\Log\Exception\RuntimeException as LogExceptionRuntimeException;
use Laminas\Log\Logger;
use Laminas\Mvc\Exception\RuntimeException as MvcExceptionRuntimeException;
use Laminas\Mvc\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;
use Laminas\Mvc\Exception\DomainException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\Stdlib\Exception\DomainException as ExceptionDomainException;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerExceptionInterface;
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
                $filter        = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data          = $form->getData();
                $data->ownerId = $this->user->id;
                $data->title   = $filter->filter($data->label);
                if (! isset($data->parentId)) {
                    $data->route  = 'page';
                    $data->params = Encoder::encode(['title' => $data->title]);
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

    /**
     * Note: during this routine the ownerId should be injected into a hidden field
     * so as not to change the owner based on who edits the page
     */
    public function updateAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->formManager->build(PageForm::class, ['mode' => FormInterface::CREATE_MODE]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager', ['action' => 'update'])
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $filter       = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data         = $form->getData();
                $data->userId = $this->user->id;
                $data->title  = $filter->filter($data->label);
                if (! isset($data->parentId)) {
                    $data->route  = 'page';
                    $data->params = Encoder::encode(['title' => $data->title]);
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
}
