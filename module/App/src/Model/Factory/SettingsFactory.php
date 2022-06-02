<?php

declare(strict_types=1);

namespace App\Model\Factory;

use App\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class SettingsFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Settings
    {
        $config = $container->get('config');
        return new Settings($config['app_settings']);
    }
}
