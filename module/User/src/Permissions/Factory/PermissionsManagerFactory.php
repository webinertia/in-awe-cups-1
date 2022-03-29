<?php

declare(strict_types=1);

namespace User\Permissions\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Config\Config;
use Laminas\EventManager\EventManager;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\Roles;
use User\Permissions\PermissionsManager;

class PermissionsManagerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PermissionsManager
    {
        $config = new Config($container->get('Config'));
        return new PermissionsManager(new Acl(), new Roles($config->db->user_roles_table_name, $container->get(EventManager::class), $config), $config);
    }
}
