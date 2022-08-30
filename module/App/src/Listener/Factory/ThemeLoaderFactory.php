<?php

declare(strict_types=1);

namespace App\Listener\Factory;

use App\Listener\ThemeLoader;
use App\Model\Theme;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Resolver\TemplatePathStack;
use Psr\Container\ContainerInterface;

class ThemeLoaderFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ThemeLoader
    {
        return new $requestedName($container->get(Theme::class), $container->get(TemplatePathStack::class));
    }
}
