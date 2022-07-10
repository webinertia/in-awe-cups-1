<?php

/** This factory can be used to create the majority of controllers */

declare(strict_types=1);

namespace App\Controller\Factory;

use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\DispatchableInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use User\Db\UserGateway;

class AppControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): DispatchableInterface {
        $controller = new $requestedName($container->get('config'), $container->get(UserGateway::class));
        $controller->setLogger($container->get(LoggerInterface::class));
        return $controller;
    }
}
