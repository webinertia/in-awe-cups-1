<?php

declare(strict_types=1);

namespace App\Db\DbGateway;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;

final class LogGateway extends TableGateway
{
    use GatewayTrait;
}
