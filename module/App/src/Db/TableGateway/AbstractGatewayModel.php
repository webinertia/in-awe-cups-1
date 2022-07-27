<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use Laminas\Stdlib\ArrayObject;

abstract class AbstractGatewayModel extends ArrayObject
{
    public function __construct(array $data = [])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    }
}
