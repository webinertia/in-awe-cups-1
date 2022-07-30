<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use Laminas\Stdlib\ArrayObject;
use User\Acl\ResourceAwareTrait;

final class Setting extends ArrayObject implements ModelInterface
{
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'settings';

    public function getOwnerId(): mixed
    {
        return null;
    }
}
