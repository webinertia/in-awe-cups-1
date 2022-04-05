<?php

declare(strict_types=1);

namespace Application\Model\Factory;

use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SettingsFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Settings
    {
        $config = $container->get('config');
        return new Settings($config['app_settings']);
    }
}
