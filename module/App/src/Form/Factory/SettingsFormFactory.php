<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\SettingsForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class SettingsFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SettingsForm
    {
        return new $requestedName($options ?? [], $container->get('config')['app_settings']);
    }
}
