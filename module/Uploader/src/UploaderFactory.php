<?php

declare(strict_types=1);

namespace Uploader;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Uploader\Adapter\TableGatewayAdapter;
use Uploader\AdapterPluginManager;

class UploaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $pluginManager = $container->get(AdapterPluginManager::class);
        if ($container->has(Acl::class)) {
            $acl = $container->get(Acl::class);
        }
        if ($container->has(AuthenticationService::class)) {
            $auth = $container->get(AuthenticationService::class);
        }
        return new Uploader($pluginManager->get(TableGatewayAdapter::class), $acl, $auth);
    }
}
