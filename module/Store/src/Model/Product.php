<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Log\LogEvent;
use App\Model\ModelTrait;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Db\TableGateway\ProductsTable;
use Store\Model\Image;
use Store\Model\OptionsPerProduct;
use Store\Model\ProductOptions;

class Product extends AbstractGatewayModel
{
    use ModelTrait;

    /** @var ProductsByCategoryTable $productsByCategoryTable */
    protected $productsByCategoryTable;
    /** @var OptionsPerProduct $optionsLookup*/
    protected $optionsLookup;
    /** @var Image $image*/
    protected $image;

    // string table names for use within queries
    /** @var string $c */
    private $c;
    /** @var string $i */
    private $i;
    /** @var string $t */
    private $t;

    public function __construct(
        ?ProductsTable $gateway = null,
        ?ProductsByCategoryTable $productsByCategoryTable = null,
        ?OptionsPerProduct $optionsLookup = null,
        ?Image $image = null,
        ?array $config = null
        ) {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
            $this->t = $this->gateway->getTable();
            $this->c = $config['db']['store_categories_table_name'];
            $this->i = $config['db']['store_image_table_name'];
        }
        if ($productsByCategoryTable !== null) {
            $this->productsByCategoryTable = $productsByCategoryTable;
        }
        if ($optionsLookup !== null) {
            $this->optionsLookup = $optionsLookup;
        }
        if ($image !== null) {
            $this->image = $image;
        }
    }

    public function fetchAllByCategory(string $category, bool $fetchArray = true): ResultSetInterface|array
    {
        $where  = new Where();
        $select = $this->image->getGateway()->getSql()->select();
        $where->equalTo('category', $this->t . '.category');
        $select->join(
            $this->t,
            $this->t . '.id = ' . $this->i . '.productId',
            ['id', 'cost', 'label', 'title'],
            Select::JOIN_LEFT_OUTER,
        );
        $select->columns(['fileName']);
        $select->where($where);
        $result = $this->image->getGateway()->selectWith($select)->toArray();
        return $result;

        // $where = new Where();
        // $where->equalTo($this->t . '.category', $category);
        // $select = $this->gateway->getSql()->select();
        // $select->join(
        //     ['i' => $this->i],
        //     $this->t . '.id = i.productId',
        //     //$this->i . '.productId = ' . $this->t . '.id',
        //     ['fileName', 'productId', 'type'],
        //    Select::JOIN_LEFT_OUTER
        // );
        // $select->order(['fileName', 'label']);
        // $select->columns(
        //     [
        //         'label','id', 'cost', 'title', 'category'
        //     ]
        // );
        //$select->quantifier(Select::QUANTIFIER_DISTINCT);
        //$select->where($where);
        return $fetchArray ? $this->gateway->selectWith($select)->toArray() : $this->gateway->selectWith($select);
    }

    public function fetchAllFiltered(): ResultSetInterface|array
    {
        $where  = new Where();
        $select = $this->gateway->getSql()->select();

        return $result;
    }

    public function fetchOptions(int|string $id, string $optionGroup = null)
    {
        $select = $this->optionsLookup->getGateway()->getSql()->select();
        $select->where(['productId' => $id]);
        if ($optionGroup !== null) {
            $select->where(['optionGroup' => $optionGroup]);
        }
        return $this->optionsLookup->getGateway()->selectWith($select);
    }

    public function fetchWithOptions(int|string $id)
    {
        $select = $this->gateway->getSql()->select();
        $select->join(
            ['o' => 'store_options_per_product'],
            'store_products.id = o.productId',
            ['optionGroup', 'optionValue'],
            Select::JOIN_INNER,
        );
        //$result = $this->gateway->selectWith($select);
        return $this->gateway->selectWith($select);
    }

    public function fetchMaxCost(?string $category = 'all', ?bool $roundUp = false, ?int $roundToDigit = 1): int|float
    {
        $where  = new Where();

        $select = $this->gateway->getSql()->select();
        $select->columns(
            [
                'category',
                'maxPrice' => 'cost',
            ]
        );
        if ($category !== 'all' && $category !== null) {
            $where->equalTo('category', $category);
        }
        $select->order(['maxPrice DESC'])->limit(1);
        $select->where($where);
        $max = (float) $this->gateway->selectWith($select)->current()->maxPrice;
        if ($roundUp) {
            return $this->roundToGivenDigit($max, $roundToDigit);
        }
        return $max;
    }

    public function save(Product|array $data)
    {
        if ($data instanceof Product) {
            $data = $data->getArrayCopy();
        }
        try {
            // decide if this is insert or update
            if(empty($data['id']) || $data['id'] === 0 || $data['id'] === '0') {
                unset($data['id']);
                return $this->gateway->insert($data);
            }
            else {
                return $this->gateway->update($data, ['id' => $data['id']]);
            }

        } catch (\Throwable $th) {
            $this->getEventManager()->trigger(LogEvent::NOTICE, $th->getMessage());
        }

    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}