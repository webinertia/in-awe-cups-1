<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use Laminas\Stdlib\ArrayObject;

final class Setting extends ArrayObject implements ModelInterface
{
    protected $resourceId = 'settings';

    public function getResourceId()
    {
        return $this->resourceId;
    }
}
