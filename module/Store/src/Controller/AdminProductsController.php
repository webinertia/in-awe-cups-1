<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Store\Db\TableGateway\CategoriesTable;
use Laminas\Form\FormElementManager;
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Db\TableGateway\ProductsTable;
use Store\Model\Product;
use Store\Form\ProductForm;
use Uploader\Fieldset\UploaderAwareMultiFile;

use function array_merge_recursive;

class AdminProductsController extends AbstractAppController implements AdminControllerInterface
{
    protected CategoriesTable $categoriesTable;
    protected ProductsTable $productsTable;
    protected ProductForm $form;
    protected Product $product;
    protected array $uploadConfig = [];
    /** @var string $resourceId */
    protected $resourceId = 'admin';

    /** @return void */
    public function __construct(
        array $config,
        CategoriesTable $categoriesTable,
        ProductsTable $productsTable,
        Product $product,
        FormElementManager $formElementManager
    ) {
        parent::__construct($config);
        $this->categoriesTable = $categoriesTable;
        $this->productsTable   = $productsTable;
        $this->product         = $product;
        $this->form            = $formElementManager->get(ProductForm::class);
        // $this->uploadConfig['upload-config']['module'] = 'store';
        // $this->uploadConfig['upload-config']['type'] = 'products';
    }

    public function indexAction(): ModelInterface
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        return $this->view;
    }

    public function manageSettings()
    {
    }

    public function managerAction(): ModelInterface
    {
        // Refactor this into individual action since were using ajax
        // This action is just the central endpoint for accessing all subactions
        // This is moving to the indexAction for simplification
        $sessionContainer = new Container();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $data = [];

        $data['product-info']['userId'] = $this->userService->id;

        $product = $this->getService(Product::class);
        $id      = $this->params('id', null);
        /**
         * Even though we will not be creating the product here, we need to
         * setup the form and populate it with its default values
         * along with setting the correct action attribute so that it
         * will post to the correct action when submitted
         */
        $product->id               = $id;
        $product->name             = '';
        $product->userId           = $this->userService->id;
        $product->categoryId       = null;
        $product                   = $product->save($product);
        $sessionContainer->product = $product->toArray();

        $data['product-info']['id'] = $product->id;

        $this->form->setAttribute(
            'action',
            $this->url()->fromRoute(
                'admin.store/products/manager',
                ['action' => 'edit', 'id' => $product->id]
            )
        );
        $this->form->setData(array_merge_recursive($this->uploadConfig, $data));
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function createAction()
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if ($this->request->isPost()) {
            $postData = $this->request->getPost();
            $this->form->setData($postData->toArray());
            /**
             * @var \Application\Form\Fieldset\FieldsetTrait $fieldset
             */
            $fieldset        = $this->form->get('product-info');
            $validationGroup = $fieldset->getElementNames();
            $this->form->setValidationGroup(['product-info' => $validationGroup]);
            if ($this->form->isValid()) {
                // get the data from the form
                $data = $this->form->getData();
                try {
                    $this->product->exchangeArray($data['product-info']);
                    $this->product->save($this->product);
                    $this->form->remove('product-info');
                    $imageFieldset = $this->sm->get(UploaderAwareMultiFile::class);
                    $imageFieldset->init();
                    $this->form->add($imageFieldset);
                    $this->form->setData($data['upload-config']);
                } catch (\Throwable $th) {
                    $this->logger->log(6, $th->getMessage());
                }
                $step = 'upload-files';
            } else {

            }
        } else {

        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function editAction(): ModelInterface
    {
        $id            = $this->params('id');
        $this->product = $this->product->fetchByColumn('id', $id);
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->request->isPost()) {
            $this->form->setAttribute(
                'action',
                $this->url()->fromRoute(
                    'admin.store/product/manager',
                    ['action' => 'edit', 'id' => $id]
                )
            );
            $this->form->setdata(array_merge_recursive($this->uploadConfig, $this->product->toArray()));
        }
        return $this->view;
    }

    public function uploadImagesAction()
    {
    }

    public function deleteAction()
    {
    }
}
