<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceManager;

final class ServiceLocator extends AbstractPlugin
{
    /** @var ServiceManager $sm */
    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /** @return ServiceManager|AbstractPluginManager|array<mixed> */
    public function __invoke(string $serviceName)
    {
        return $this->sm->get($serviceName);
    }
}
