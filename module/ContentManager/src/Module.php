<?php

declare(strict_types=1);

namespace ContentManager;

use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;

final class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootStrap(MvcEvent $e): void
    {
        $sm = $e->getApplication()->getServiceManager();
        /**
         * We want this to be as early as possible. Since this is the first module that
         * loads it happens here
         */
        $sm->get(Container::class);
    }
}
