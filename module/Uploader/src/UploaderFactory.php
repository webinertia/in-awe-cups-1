<?php

declare(strict_types=1);

namespace Uploader;

use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Acl;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Uploader\Adapter\TableGatewayAdapter;
use Uploader\AdapterPluginManager;

final class UploaderFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Uploader
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
