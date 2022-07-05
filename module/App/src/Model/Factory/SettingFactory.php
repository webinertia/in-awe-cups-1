<?php

declare(strict_types=1);

namespace App\Model\Factory;

use App\Model\Setting;
use App\Model\Theme;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class SettingFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Setting
    {
        return new $requestedName($options ?? [], $container->get('config')['app_settings']);
    }
}
