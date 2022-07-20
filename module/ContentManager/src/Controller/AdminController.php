<?php

declare(strict_types=1);

namespace ContentManager\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\FormInterface;
use ContentManager\Db\PageGateway;
use ContentManager\Form\PageForm;
use ContentManager\Model\Page;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\SeparatorToDash;
use Laminas\Form\FormElementManager;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\ViewModel;
use RuntimeException;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'page';

    public function createAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->getService(FormElementManager::class)->get()->build(
            PageForm::class,
            ['mode' => FormInterface::CREATE_MODE]
        );
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager/create')
        );
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $gateway       = $this->getService(PageGateway::class);
                $filter        = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data          = $form->getData();
                $data->ownerId = $this->identity()->getIdentity()->id;
                $data->title   = $filter->filter($data->label);
                $data->route   = 'page';
                $data->params  = ['title' => $data->title];
                try {
                    $result = $gateway->insert($data->getArrayCopy());
                    if (! $result) {
                        throw new RuntimeException('Page Not saved');
                    }
                    $headers = $this->response->getHeaders();
                    $headers->addHeaderLine('Content-Type', 'application/json');
                    $this->view->setVariables(['success' => true, 'message' => ['message' => 'Page saved']]);
                } catch (RuntimeException $e) {
                    $this->getLogger()->error($e->getMessage(), $this->identity()->getIdentity()->getLogData());
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
    public function editAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->getService(FormElementManager::class)->build(
            PageForm::class,
            ['mode' => FormInterface::CREATE_MODE]
        );
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager/edit', ['title' => $this->params('title')])
        );
        $title      = $this->params('title');
        $navigation = $this->getService(Navigation::class);
        $page       = $navigation->findOneByTitle($title);
        $model      = new Page();
        $bindData   = $page->toArray();
        $model->exchangeArray($bindData);
        $form->bind($model);
        if ($this->request->isPost()) {
            $gateway = $this->getService(PageGateway::class);
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $filter        = (new FilterChain())->attach(new StringToLower())->attach(new SeparatorToDash());
                $data          = $form->getData();
                $data->ownerId = $this->identity()->getIdentity()->id;
                $data->title   = $filter->filter($data->label);
                $data->route   = 'page';
                try {
                    $result = $gateway->update($data->getArrayCopy(), ['id' => $data->id]);
                    if (! $result) {
                        throw new RuntimeException('Page Not saved');
                    }
                    $this->flashMessenger()->addSuccessMessage('Page saved');
                    $this->view->setvariables(
                        [
                            'success' => true,
                            'data'    => ['href' => $this->url()->fromRoute('page', ['title' => $data->title])],
                        ]
                    );
                    $headers = $this->response->getHeaders();
                    $headers->addHeaderLine('Content-Type', 'application/json');
                } catch (RuntimeException $e) {
                    $this->critical($e->getMessage());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function deleteAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->view->setTerminal(true);
        }
        if ($this->isAllowed($this)) {
            $id         = $this->params('id');
            $navigation = $this->getService(Navigation::class);
            $page       = $navigation->findOneById($id);
            $gateway    = $this->getService(PageGateway::class);
            try {
                $result = $gateway->delete(['id' => $page->id]);
                if (! $result) {
                    $this->error('Page Delete error');
                    $this->flashMessenger()->addErrorMessage('Page not deleted');
                    $this->view->setVariable(
                        'data',
                        [
                            'href'    => $this->url()->fromRoute('page', ['title' => $page->title]),
                            'message' => 'Page not deleted',
                        ]
                    );
                }
                $this->flashMessenger()->addSuccessMessage('Page deleted');
                $this->view->setVariable('data', ['href' => $this->url()->fromRoute('home')]);
            } catch (RuntimeException $e) {
                $this->error($e->getMessage());
            }
        }
        return $this->view;
    }

    public function uploadImagesAction(): ViewModel
    {
        if (! $this->isAllowed($this)) {
            $this->flashMessenger()->addErrorMessage('You are not allowed to upload images');
            $this->response->setStatusCode(403);
        }
        $data   = [];
        $config = $this->getService('config')['page_upload_paths'];
        $this->view->setTerminal(true);
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if ($this->request->isPost()) {
            $data = (array) $this->request->getFiles();
        }
        $localPath  = $config['local_path'];
        $publicPath = $config['public_path'];
        $fileFilter = new RenameUpload();
        // set it to randomize the file name
        $fileFilter->setRandomize(true);
        // this sets the path for directory and the base file name used for all page Images
        $fileFilter->setTarget($this->basePath . $localPath . 'page_image');
        // maintain the original file extension
        $fileFilter->setUseUploadExtension(true);
        // perform the move and rename on the file
        $uploaded       = $fileFilter->filter($data['file']);
        $baseNameFilter = new BaseName();
        // grab just the file name so it can be stored in the profile table
        $baseName = $baseNameFilter->filter($uploaded['tmp_name']);
        // the view needs this data in this format for tinymce to work
        $data = ['location' => $publicPath . $baseName];
        $this->view->setVariable('data', $data);
        return $this->view;
    }
}
