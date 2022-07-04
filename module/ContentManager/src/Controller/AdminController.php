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
use Laminas\Json\Encoder;
use Laminas\Log\Logger;
use Laminas\Navigation\Navigation;
use Laminas\View\Model\ViewModel;
use RuntimeException;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'pages';
    /** @var Page $page */
    /** @var Pages $pages */
    /** @var PageForm $form */

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function createAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->service()->get(FormElementManager::class)->build(PageForm::class, ['mode' => FormInterface::CREATE_MODE]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager/create')
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $gateway       = $this->service()->get(PageGateway::class);
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
    public function editAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $form = $this->service()->get(FormElementManager::class)->build(PageForm::class, ['mode' => FormInterface::CREATE_MODE]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin.content/manager/edit', ['title' => $this->params('title')])
        );
        $title      = $this->params('title');
        $navigation = $this->service()->get(Navigation::class);
        $page       = $navigation->findOneByTitle($title);
        $form->bind($page);
        if ($this->request->isPost()) {
            $gateway = $this->service()->get(PageGateway::class);
            // $model   = $this->service()->get(Page::class);
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
                    $this->logger->log(Logger::EMERG, $e->getMessage(), $this->user->getLogData());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function uploadImagesAction(): ViewModel
    {
        if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'upload.images')) {
            $this->flashMessenger()->addErrorMessage('You are not allowed to upload images');
            $this->response->setStatusCode(403);
        }
        $this->view->setTerminal(true);
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if ($this->request->isPost()) {
            $data = (array) $this->request->getFiles();
        }
        $localPath  = '/public/modules/contentmanager/page/content/images/';
        $publicPath = '/modules/contentmanager/page/content/images/';
        $fileFilter = new RenameUpload();
        // set it to randomize the file name
        $fileFilter->setRandomize(true);
        // notice this sets the path for directory and the base file name used for all profile Images
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
