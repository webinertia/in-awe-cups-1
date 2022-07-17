<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\ThemeSettingsForm;
use App\Model\Theme;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class ThemeSettingsFormFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ThemeSettingsForm
    {
        return new $requestedName($container->get(Theme::class), $options ?? []);
    }
}
