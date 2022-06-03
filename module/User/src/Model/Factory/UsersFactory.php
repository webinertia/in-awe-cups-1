<?php

declare(strict_types=1);

namespace User\Model\Factory;

use Laminas\Config\Config;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

final class UsersFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Users
    {
        $config = new Config($container->get('Config'));
        return new Users(
            $config->db->users_table_name,
            $container->get(ModelManager::class),
            $container->get(EventManager::class),
            $config,
            $container->get(Logger::class)
        );
    }
}
