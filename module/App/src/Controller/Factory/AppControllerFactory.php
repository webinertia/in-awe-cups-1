<?php

/** This factory can be used to create the majority of controllers */

declare(strict_types=1);

namespace App\Controller\Factory;

use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\DispatchableInterface;
use User\Db\UserGateway;
use Psr\Container\ContainerInterface;

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
        return new $requestedName(
            $container->get('config'),
            $container->get(Logger::class),
            $container->get(UserGateway::class)
        );
    }
}
