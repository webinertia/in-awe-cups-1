<?php

declare(strict_types=1);

namespace Uploader\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Uploader\Adapter\TableGatewayAdapter;
use Uploader\AdapterPluginManager;

class TableGatewayAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /**
         * @var AdapterPluginManager $pluginManager
         */
        $pluginManager = $container->get(AdapterPluginManager::class);
        if ($container->has(Logger::class)) {
            $logger = $container->get(Logger::class);
        }
        return new TableGatewayAdapter($container->get(AdapterInterface::class), $container->get('config'), $container->get(EventManager::class), $logger ?? null);
    }
}
