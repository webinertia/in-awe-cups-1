<?php

declare(strict_types=1);

namespace Store\Db\TableGateway;

use App\Db\TableGateway\TableGateway;
use App\Db\TableGateway\TableGatewayTrait;
use Laminas\Db\ResultSet\ResultSet;
use Store\Model\Category;

class CategoriesTable extends TableGateway
{
    public function fetchSelectValueOptions()
    {
        $data = [];
        $resultSet = $this->select();
        foreach($resultSet as $row)
        {
            $data[$row->id] = $row->label;
        }
        return $data;
    }
}