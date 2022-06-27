<?php

declare(strict_types=1);

namespace App\Controller\Plugin\Factory;

use App\Controller\Plugin\ServiceLocator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class ServiceLocatorFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ServiceLocator
    {
        return new ServiceLocator($container);
    }
}
