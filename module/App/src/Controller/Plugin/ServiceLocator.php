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

    /**
     * @param string|class-string $serviceName
     * @return mixed Entry
     * */
    public function __invoke(string $serviceName)
    {
        return $this->getService($serviceName);
    }

    /** @return ServiceManager|AbstractPluginManager|array<string|class-string, Entry> */
    public function getService(string $serviceName)
    {
        return $this->sm->get($serviceName);
    }
}
