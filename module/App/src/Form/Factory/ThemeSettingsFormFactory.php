<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\ThemeSettingsForm;
use App\Model\Theme;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ThemeSettingsFormFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ThemeSettingsForm
    {
        return new $requestedName($container->get(Theme::class), $options ?? []);
    }
}