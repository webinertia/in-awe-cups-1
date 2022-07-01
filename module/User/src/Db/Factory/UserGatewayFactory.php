<?php

declare(strict_types=1);

namespace User\Db\Factory;

use Laminas\Db\ResultSet\Exception\InvalidArgumentException;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ObjectPropertyHydrator as Hydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Db\UserGateway;
use User\Service\UserService as UserInterface;

final class UserGatewayFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserGateway
    {
        $config             = $container->get('config');
        $resultSetPrototype = new HydratingResultSet(new Hydrator());
        $resultSetPrototype->setObjectPrototype(new UserInterface());
        return new UserGateway($config['db']['users_table_name'], null, $resultSetPrototype);
    }
}
