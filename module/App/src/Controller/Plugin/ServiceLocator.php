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

    public function __invoke()
    {
        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this->serviceLocator->{$name}(...$arguments);
    }
}
