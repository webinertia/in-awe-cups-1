<?php
declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelTrait;
use Store\Db\TableGateway\ProductsByCategoryTable;

class ProductByCategory extends AbstractGatewayModel
{
    use ModelTrait;

    public function __construct(?ProductsByCategoryTable $gateway = null)
    {
        parent::__construct([]);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
    }
}