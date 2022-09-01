<?php

declare(strict_types=1);

namespace App\Session;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\SessionManager;
use Psr\Container\ContainerInterface;

class SessionManagerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SessionManager
    {
        $config = $container->has("config") ? $container->get("config") : [];
        $config = $config["session_config"] ?? [];

        $sessionConfig = ! empty($config["config_class"]) ? new $config["config_class"]() : new SessionConfig();
        $sessionConfig->setOptions($config);
        return new $requestedName($sessionConfig);
    }
}
