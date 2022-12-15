<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use Laminas\Db\TableGateway\TableGateway;
use Store\Db\TableGateway\ProductsByCategoryTable;
use Store\Db\TableGateway\ProductOptionsTable;

class ProductOptions extends AbstractGatewayModel implements ModelInterface
{
    use ModelTrait;

    public function __construct(?ProductOptionsTable $productOptionsTable = null)
    {
        parent::__construct([]);
        if ($productOptionsTable !== null) {
            $this->gateway = $productOptionsTable;
        }
    }

    public function fetchOptionsById(int $id)
    {

    }

    public function fetchByOptionGroup(string $optionGroup)
    {

    }

    public function fetchByProductGroup(string $productGroup)
    {

    }

    public function fetchOptionGroups($fetchArray = false)
    {
        $select = $this->gateway->getSql()->select();
        $select->columns(['optionGroup']);
        $select->group('optionGroup');
        $result = $this->gateway->selectWith($select);
        if ($fetchArray) {
            return $result->toArray();
        }
        return $result;

    }
    /**
     *  [
     *      'id' => $id,
     *      'optionGroup' => $optionGroup,
     *  ]
     * @param array $data
     */
    public function saveProductOptions(array $data)
    {

    }
}
