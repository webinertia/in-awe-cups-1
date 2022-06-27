<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use Laminas\Config\Config;

final class Settings extends Config implements ModelInterface
{
    protected const RESOURCE_ID        = 'settings';
    protected const SETTINGS_NAMESPACE = 'app_settings';
    public function getResourceId(): string
    {
        return 'settings';
    }
}
