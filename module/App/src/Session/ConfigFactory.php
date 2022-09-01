<?php

declare(strict_types=1);

namespace App\Session;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Config\SessionConfig;
use Psr\Container\ContainerInterface;

class ConfigFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SessionConfig
    {
        $config = $container->get('config')['session_config'] ?? [];
        $class  = SessionConfig::class;
        $class  = new $class();
        $class->setOptions($config);
        return $class;
    }
}
