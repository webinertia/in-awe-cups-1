<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use Laminas\Config\Config;
use User\Acl\ResourceAwareTrait;

final class Settings extends Config implements ModelInterface
{
    use ResourceAwareTrait;

    public const SETTINGS_NAMESPACE = 'app_settings';

    /** @var string $resourceId */
    protected $resourceId = 'settings';

    public function getOwnerId(): mixed
    {
        return null;
    }
}
