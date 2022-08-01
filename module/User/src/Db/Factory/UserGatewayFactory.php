<?php

declare(strict_types=1);

namespace User\Db\Factory;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\EventManager\EventManager;
use Laminas\Hydrator\ObjectPropertyHydrator as Hydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Db\Listener\UserGatewayListener;
use User\Db\UserGateway;
use User\Service\UserService;

final class UserGatewayFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserGateway
    {
        $config             = $container->get('config');
        $resultSetPrototype = new HydratingResultSet(new Hydrator());
        $resultSetPrototype->setObjectPrototype(new UserService());
        return new $requestedName(
            $config['db']['users_table_name'],
            $container->get(EventManager::class),
            $resultSetPrototype,
            true,
            $container->get(UserGatewayListener::class)
        );
    }
}
