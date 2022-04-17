<?php

declare(strict_types=1);

namespace User\Permissions\Factory;

use Laminas\Config\Config;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Roles;
use User\Permissions\PermissionsManager;
use Webinertia\ModelManager\ModelManager;

class PermissionsManagerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PermissionsManager
    {
        $config       = new Config($container->get('Config'));
        $modelManager = $container->get(ModelManager::class);
        return new PermissionsManager(new Acl(), $modelManager->get(Roles::class), $config);
    }
}
