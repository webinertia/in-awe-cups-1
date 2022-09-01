<?php

declare(strict_types=1);

namespace App\Session;

use App\Session\Container;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Psr\Container\ContainerInterface;

class ContainerFactory implements FactoryInterface
{
    /** @param string $requestedName*/
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Container
    {
        return new $requestedName('Aurora_Context', $container->get(SessionManager::class));
    }
}
