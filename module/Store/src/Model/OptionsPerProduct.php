<?php

declare(strict_types=1);

namespace Store\Model;


use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use ArrayObject;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\In;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\AdapterPluginManager;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\ArrayObject as Ao;
use Laminas\Stdlib\ArrayUtils;
use Store\Model\Product;
use Store\Model\ProductOptions;

use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_values;
use function count;
use function explode;

class OptionsPerProduct extends AbstractGatewayModel implements ModelInterface
{
    use ModelTrait;

    protected $resourceId = 'store';

    /** @var TableGateway $productTable */
    protected $productTable;
    /** @var ProductOptions $productOptions */
    protected $productOptions;
    /** @var TableGateway $gateway */
    protected $gateway;
    /** @var int|float $step */
    protected $step;
    /** @var string $p */
    protected $p;
    /** @var string $c */
    protected $c;
    /** @var string $t */
    protected $t;
    /** @var string $i */
    protected $i;

    public function __construct(?ProductOptions $productOptions = null, ?TableGateway $gateway = null, ?array $config = null) {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
        if ($productOptions !== null) {
            $this->productOptions = $productOptions;
        }
        if ($config !== null) {
            $this->config = $config;
            $this->t    = $this->config['db']['store_options_per_product_table_name'];
            $this->c    = $this->config['db']['store_categories_table_name'];
            $this->p    = $this->config['db']['products_table_name'];
            $this->i    = $this->config['db']['store_image_table_name'];
            $this->step = $this->config['module_settings']['store']['search_options']['price_step_value'];
        }
        $this->productTable = new TableGateway('store_products', $this->gateway->getAdapter());
    }

    public function productSearch(
        ?int $productId,
        ?string $category,
        array $params,
        bool $fetchArray = true
    ): ResultSetInterface|array {
        return $this->search($productId, $category, $this->sortParams($params), null, $fetchArray);
    }

    private function sortParams(array $params): array
    {
        $inGroups  = [];
        $inOptions = [];
        $costRange = [];
        $searchOptions = [];
        // filter out everything except what is in the option lookup table
        if (isset($params['cost']) && $params['cost'] !== 'all') {
            $costRange['min']    = (float) $params['cost'] - $this->step;
            $costRange['max']    = (float) $params['cost'];
            unset($params['cost']);
            $searchOptions['costRange'] = $costRange;
        } else {
            unset($params['cost']);
        }
        foreach($params as $group => $options) {
            $inGroups = array_keys($params);
            //$inOptions[] = array_values($options);
            foreach ($options as $k => $v) {
                $inOptions[] = $v;
            }
        }
        if ($inGroups !== []) {
            $searchOptions['groupSet']  = $inGroups;
        }
        if ($inGroups !== []) {
            $searchOptions['optionSet'] = $inOptions;
        }
        return $searchOptions;
    }

    protected function search(
        ?int $productId,
        ?string $category,
        ?array $params,
        ?array $sort,
        bool $fetchArray = true,
        bool $onlyActive = true,
        bool $paginated = false
    ): ResultSetInterface|array {

        $select = new Select();
        $where  = new Where();
        $select->from(['o' => $this->t])
        ->columns(['productId', 'optionGroup', 'option'])
        ->quantifier(Select::QUANTIFIER_DISTINCT);

        if ($productId !== null) {
            $where->equalTo('productId', $productId);
        }
        if ($category !== null) {
            $where->equalTo('o.category', $category);
        }
        if (isset($params['groupSet'])) {
            $where->in('optionGroup', $params['groupSet']);
        }
        if (isset($params['optionSet'])) {
            $where->in('option', $params['optionSet']);
        }
        if (isset($params['costRange'])) {
            $where->between('p.cost', $params['costRange']['min'], $params['costRange']['max']);
        }

        $select->join(
            ['p' => $this->p],
            'o.productId = p.id',
            ['*'],
        );
        $select->join(
            ['c' => $this->c],
            'o.category = c.label',
            ['categoryId' => 'id', 'categoryTitle' => 'title']
        );
        $select->join(
            ['i' => $this->i],
            'i.productId = o.productId',
            ['fileName']
        );
        if ($onlyActive) {
            $where->equalTo('c.active', 1);
            $where->equalTo('p.active', 1);
        }
        $select->group(['o.productId']);
        if ($sort !== null) {

        }
        $select->where($where);
        $result = $this->gateway->selectWith($select);
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;
    }

    public function fetchByOptionGroup(
        int|string $productId,
        string $optionGroup,
        bool $fetchArray = true,
        ?array $columns = null
    ): array|ResultSetInterface {
        $select = $this->gateway->getSql()->select();
        if ($columns !== null) {
            $select->columns($columns);
        }
        $select->where(['productId' => $productId])->where(['optionGroup' => $optionGroup]);
        $resultSet = $this->gateway->selectWith($select);
        if (! $fetchArray) {
            return $resultSet;
        }
        return $resultSet->toArray();
    }

    public function fetchMultiCheckboxValues(
        int|string $productId,
        string $category,
        string $optionGroup,
        bool $idAsValue = false
    ): array {
        $mergedOptions      = [];
        $temp               = [];
        $columns            = ['category', 'optionGroup', 'option'];
        $totalOptions       = $this->productOptions->fetchOptions($category, $optionGroup, true, $columns);
        $totalOptionCount   = count($totalOptions);
        $currentOptions     = $this->fetchByOptionGroup($productId, $optionGroup, true, $columns);
        $currentOptionCount = count($currentOptions);

        if ($currentOptionCount <= $totalOptionCount && $currentOptionCount > 0) {
            for ($i=0; $i < $totalOptionCount; $i++) {
                foreach($currentOptions as $option) {
                    if ($option === $totalOptions[$i]) {
                        $mergedOptions[$i]['value'] = $option['option'];
                        $mergedOptions[$i]['selected'] = true;
                        $mergedOptions[$i]['label'] = $option['option'];
                        break;
                    } else {
                        $mergedOptions[$i]['value'] = $totalOptions[$i]['option'];
                        $mergedOptions[$i]['selected'] = false;
                        $mergedOptions[$i]['label'] = $totalOptions[$i]['option'];
                    }
                }
            }
        } elseif ($currentOptionCount === 0) {
            for ($i=0; $i < $totalOptionCount; $i++) {
                $mergedOptions[$i]['value'] = $totalOptions[$i]['option'];
                $mergedOptions[$i]['selected'] = false;
                $mergedOptions[$i]['label'] = $totalOptions[$i]['option'];
            }
        }
        return $mergedOptions;
    }

        /**
     * We only want to allow filtering based on options that have actually been assigned
     * to a particular product that belongs to a particular category
     * @param string $category
     * @param bool $fetchArray
     * @return ResultSetInterface|array
     * @throws ExceptionInvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchSearchableOptions(string $category, bool $fetchArray = true): Ao|array
    {
        $where  = new Where();
        $where->equalTo('category', $category);
        $select = $this->gateway->getSql()->select();
        $select->columns(['optionGroup', 'option']);
        $select->order('optionGroup');
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $groups = $this->gateway->selectWith($select)->toArray();
        $return = [];
        foreach ($groups as $option) {
            $return[$option['optionGroup']][] = $option['option'];
        }
        if ($fetchArray) {
            return $return;
        }
        return new Ao($return, Ao::ARRAY_AS_PROPS);
    }

    public function save($set)
    {
        // if (isset($set['id'])) {
        //     unset($set['id']);
        // }
        if (ArrayUtils::isList($set['productOptions'])) {
            $pSelect = $this->productTable->getSql()->select();
            $pSelect->columns(['category']);
            $pWhere  = new Where();
            $pWhere->equalTo('id', $set['productId']);
            $pSelect->where($pWhere);
            foreach ($set['productOptions'] as $key => $value) {
                if (! $this->hasRecord($set['productId'], $set['optionGroup'], $value)) {
                    $category = $this->productTable->selectWith($pSelect)->current()->category;
                    $this->gateway->insert([
                        'productId'   => $set['productId'],
                        'category'    => $category,
                        'optionGroup' => $set['optionGroup'],
                        'option'      => $value,
                    ]);
                }
            }
        }
    }

    public function hasRecord($productId, $optionGroup, $option, $returnRecord = false): bool|self
    {
        $where = new Where();
        $where->equalTo('productId', $productId)->equalTo('optionGroup', $optionGroup)->equalTo('option', $option);
        $check = $this->gateway->select($where);
        if ($check instanceof ResultSet) {
            $result = $check->current();
        }
        if ($result === null) {
            return false;
        }
        if($result instanceof self || $result instanceof ArrayObject) {
            if ($returnRecord) {
                return $result;
            }
            return true;
        }
    }
}
