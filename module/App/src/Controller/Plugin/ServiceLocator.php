<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceManager;

final class ServiceLocator extends AbstractPlugin
{
    /** @var ServiceManager $serviceLocator */
    public function __construct(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke(?string $serviceName = null): mixed
    {
        if ($serviceName === null) {
            return $this;
        }
        return $this->serviceLocator->get($serviceName);
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments): mixed
    {
        return $this->serviceLocator->{$name}(...$arguments);
    }
}
