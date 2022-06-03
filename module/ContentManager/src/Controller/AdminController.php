<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAdminController;
use App\Form\FormInterface;
use ContentManager\Form\PageForm;
use ContentManager\Model\Page;
use ContentManager\Model\Pages;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Form\FormElementManager;
use Laminas\Json\Encoder;
use Laminas\Log\Logger;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Webinertia\ModelManager\ModelManager;

final class AdminController extends AbstractAdminController
{
    /** @var Page $page */
    /** @var Pages $pages */
    /** @var PageForm $form */
    /** @return void */
    public function __construct(ModelManager $modelManager, FormElementManager $formElementManager)
    {
        $this->formManager = $formElementManager;
        $this->pages       = $modelManager->get(Pages::class);
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
        return $this->view;
    }

    public function updateAction(): ViewModel
    {
        return $this->view;
    }
}
