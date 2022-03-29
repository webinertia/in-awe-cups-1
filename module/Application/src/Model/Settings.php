<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Model\ModelTrait;
use Laminas\Config\Config;

class Settings extends Config
{
    use ModelTrait;

    const RESOURCE_ID        = 'settings';
    const SETTINGS_NAMESPACE = 'app_settings';
}
