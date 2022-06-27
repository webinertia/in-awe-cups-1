<?php

declare(strict_types=1);

namespace User\Permissions\Factory;

use Laminas\Config\Config;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Roles;
use User\Permissions\PermissionsManager;

final class PermissionsManagerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PermissionsManager
    {
        $config = new Config($container->get('Config'));
        return new PermissionsManager(new Acl(), $container->get(Roles::class), $config);
    }
}
