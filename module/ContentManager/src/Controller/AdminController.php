<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use Application\Controller\AbstractAdminController;
use ContentManager\Form\PageForm;
use ContentManager\Model\Page;
use ContentManager\Model\Pages;
use Laminas\Filter\FilterChain;
use Laminas\filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use Webinertia\ModelManager\ModelManager;

class AdminController extends AbstractAdminController
{
    /** @var Page $page */
    /** @var Pages $pages */
    /** @var PageForm $form */
    public function __construct(ModelManager $modelManager, FormElementManager $formElementManager)
    {
        $this->form = $formElementManager->get(PageForm::class);
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
        if ($this->request->isPost()) {
            $this->form->setData($this->request->getPost()['page-data']);
            if ($this->form->isValid()) {
                $filter        = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data          = $this->form->getData();
                $data['title'] = $filter->filter($data['label']);
            }
        }
        $this->view->setVariable('form', $this->form);
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
