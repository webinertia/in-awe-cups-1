<?php

declare(strict_types=1);

namespace App\Form\Fieldset\Factory;

use App\Form\Fieldset\AppSettingsFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class AppSettingsFieldsetFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AppSettingsFieldset
    {
        return new $requestedName();
    }
}
