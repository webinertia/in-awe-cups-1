<?php

declare(strict_types=1);

namespace App\View\Helper\Factory;

use App\View\Helper\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class ConfigFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): Config {
        return new $requestedName($container->get('config'));
    }
}