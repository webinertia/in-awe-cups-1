<?php

declare(strict_types=1);

namespace App\Model;

use Laminas\Config\Config;
use Webinertia\ModelManager\ModelInterface;
use Webinertia\ModelManager\ModelTrait;

final class Settings extends Config implements ModelInterface
{
    use ModelTrait;

    protected const RESOURCE_ID        = 'settings';
    protected const SETTINGS_NAMESPACE = 'app_settings';
}
