<?php

declare(strict_types=1);

namespace User\View\Helper\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Permissions\PermissionsManager;
use User\View\Helper\AclAwareControl;

class AclAwareControlFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AclAwareControl
    {
         return new AclAwareControl(
             $container->get(PermissionsManager::class),
             $container->get(AuthenticationService::class)
         );
    }
}
