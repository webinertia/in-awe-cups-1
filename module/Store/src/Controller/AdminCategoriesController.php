<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Filter\LabelToTitle;
use App\Upload\UploadAwareInterface;
use App\Upload\UploadEvent;
use Laminas\Filter\FilterPluginManager;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Store\Form\CategoryForm;
use Store\Model\Image;
use Store\Model\Category;
use Throwable;

use function array_merge_recursive;

class AdminCategoriesController extends AbstractAppController
implements AdminControllerInterface, UploadAwareInterface
{
    protected Category $category;
    protected Image $imageModel;
    /** @var string $resourceId */
    protected $resourceId = 'admin';
    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var LabelToTitle $labelToTitleFilter */
    protected $labelToTitleFilter;

    public function __construct(
        Category $category,
        FilterPluginManager $filterPluginManager,
        FormElementManager $formManager,
        Image $imageModel,
        array $config
    ) {
        parent::__construct($config);
        $this->category    = $category;
        $this->labelToTitleFilter = $filterPluginManager->get(LabelToTitle::class);
        $this->formManager = $formManager;
        $this->form = $this->formManager->get(CategoryForm::class);
        $this->imageModel  = $imageModel;
    }

    public function indexAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function createAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $jsonModel = new JsonModel();
        }
        $this->form->setAttribute('action', $this->url()->fromRoute('admin.store/manage/categories', ['action' => 'create']));
        if ($this->request->isPost()) {
            $this->form->setData(array_merge_recursive($this->request->getPost()->toArray(), $this->request->getFiles()->toArray()));
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                try {
                    $this->category->exchangeArray($data['category-data']);
                    $this->category->title = $this->labelToTitleFilter->filter($this->category->label);
                    $this->category->save($this->category);
                    $this->imageModel->categoryId = $this->category->getLastInsertId();
                    $this->imageModel->setUploadType(IMAGE::CATEGORY_TYPE);
                    $this->getEventManager()->trigger(UploadEvent::EVENT_UPLOAD, $this->imageModel, $data['image-data']['image-files']);
                    $jsonModel->setVariables(['success' => true, 'message' => [$this->category->label . ' was created successfully.']]);
                    return $jsonModel;
                } catch (Throwable $th) {
                    $jsonModel->setVariables(['success' => false, 'message' => $th->getMessage()]);
                }
            }
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }
}
