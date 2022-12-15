<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractApiController;
use Dojo\Data;
use Laminas\Form\FormElementManager;
use Laminas\Http\Header\ContentRange;
use Laminas\View\Model\JsonModel;
use Store\Api\Form\APiProductForm;
use Store\Model\Product;

final class ProductsApiController extends AbstractApiController
{
    /** @var Data $dojoData */
    protected $dojoData;
    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var JsonModel $view */
    protected $view;
    /** @var Product $product */
    protected $product;
    /** @var ProductOptions $productOptions */
    protected $productOptions;

    public function __construct(FormElementManager $formManager, Product $product, array $config)
    {
        parent::__construct($config);
        $this->setIdentifierName('id');
        $this->formManager = $formManager;
        $this->product     = $product;
        $this->dojoData    = new Data($this->getIdentifierName(), null, 'label');
    }

    public function get($id)
    {
        if ($id !== null) {
            // return the object
            $this->dojoData->setItem($this->product->fetchByColumn('id', $id)->toArray());
            return $this->dojoData->toArray();
        } else {
            return $this->getList();
        }
    }

    public function update($id, $data)
    {
        $this->product->exchangeArray($data);
        try {
            $this->product->save($this->product);
            $this->response->setStatusCode(202);
            return new JsonModel($this->dojoData->setItem($this->product->fetchByColumn('id', $id))->toArray());
        } catch (\Throwable $th) {
            $this->response->setStatusCode(500);
        }
    }

    public function create($data)
    {
        // send success header

        // send failure header
    }

    public function delete($id)
    {
        // delete resource
        // set response code $this->response->setStatusCode($int);
        $result = $this->product->delete(['id' => $id]);
        if ($result) {
            $this->response->setStatusCode(204);
        }
    }

    public function getList()
    {
        $this->dojoData->setItems($this->product->fetchAll(true));
        $headers = $this->response->getHeaders();
        $headers->addHeaderLine('Content-Range', '0-1/1');

        return new JsonModel($this->dojoData->toArray());
    }

    public function deleteList($data)
    {
        // delete resource
        // set response code
    }
}