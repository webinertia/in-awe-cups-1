<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Psr\Container\ContainerInterface;

final class ServiceLocator extends AbstractPlugin
{
    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(?string $serviceName = null): mixed
    {
        if ($serviceName === null) {
            return $this;
        }
        return $this->container->get($serviceName);
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments): mixed
    {
        return $this->container->{$name}(...$arguments);
    }
}
