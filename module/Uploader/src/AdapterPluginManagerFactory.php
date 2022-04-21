<?php

declare(strict_types=1);

namespace Uploader;

use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Uploader\AdapterPluginManager;

use function is_array;

class AdapterPluginManagerFactory implements FactoryInterface
{
    /**
     * @param string $name
     * @param null|mixed[] $options
     * @return object
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ContainerModificationsNotAllowedException
     */
    public function __invoke(ContainerInterface $container, $name, ?array $options = null)
    {
        $pluginManager = new AdapterPluginManager($container, $options ?: []);

        // If we do not have a config service, nothing more to do
        if (! $container->has('config')) {
            return $pluginManager;
        }

        $config = $container->get('config')['upload_manager'] ?? null;

        // If we do not have serializers configuration, nothing more to do
        if (! is_array($config)) {
            return $pluginManager;
        }
        // Wire service configuration
        (new Config($config))->configureServiceManager($pluginManager);

        return $pluginManager;
    }

    /**
     * @param mixed $name
     * @param mixed $requestedName
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: AdapterPluginManager::class, $this->creationOptions);
    }

    /**
     * laminas-servicemanager v2 support for invocation options.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}