<?php

declare(strict_types=1);

namespace ContentManager\Db;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;

final class PageGateway extends TableGateway
{
    use GatewayTrait;

    public function fetchMenu(): array
    {
        $result = $this->select();
        $pages  = [];
        foreach ($result as $row) {
            $pages[] = $row->toArray();
        }
        return $pages;
    }
}
