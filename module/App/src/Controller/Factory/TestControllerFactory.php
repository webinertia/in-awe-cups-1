<?php

declare(strict_types=1);

namespace App\Controller\Factory;

use App\Controller\TestController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class TestControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): TestController
    {
        return new TestController($container);
    }
}
