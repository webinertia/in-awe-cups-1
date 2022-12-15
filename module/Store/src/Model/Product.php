<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelTrait;
use Laminas\Db\Sql\Select;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Db\TableGateway\ProductsTable;
use Store\Model\ProductOptions;

class Product extends AbstractGatewayModel
{
    use ModelTrait;

    /** @var ProductsByCategoryTable $productsByCategoryTable */
    protected $productsByCategoryTable;

    public function __construct(?ProductsTable $gateway = null, ?ProductsByCategoryTable $productsByCategoryTable = null)
    {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
        if ($productsByCategoryTable !== null) {
            $this->productsByCategoryTable = $productsByCategoryTable;
        }
    }

    public function fetchWithOptions(int $id)
    {
        $select = new Select();
        $select->from(['p' => 'store_products'])
        ->join(['o' => 'store_options_per_product'], 'p.id = o.productId', ['p.id', 'p.label', 'o.optionValue'], Select::JOIN_OUTER)
        ->where('p.id > 0');// this errors because of the where clause,
        $result = $this->gateway->selectWith($select);
        return $result;
    }

    public function save(Product $product)
    {
        try {
            $data = $product->getArrayCopy();
            $lookupData = [];
            $lookupData['categoryId'] = $data['categoryId'];
            unset($data['categoryId']);
            // decide if this is insert or update
            if(empty($data['id'])) {
                unset($data['id']);
                $result = $this->gateway->insert($data);
                $data['id'] = $lookupData['productId'] = $this->gateway->getLastInsertValue();
                $data['categoryId'] = $lookupData['categoryId']; // make sure the returned object has the correct context
                $product->exchangeArray($data);
               // $result = $this->lookupTable->insert($lookupData);
                return $product;
            }
            else {
                // we need to run an update with a join
                $this->gateway->update($data, ['id' => $data['id']]);
            }

        } catch (\Throwable $th) {
            //throw $th;
            //$this->logger->log(6, $th->getMessage());
        }

    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}