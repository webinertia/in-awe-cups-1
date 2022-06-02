<?php

declare(strict_types=1);

namespace User;

use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use User\Listener\LayoutListener;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $app = $e->getApplication();
        /** @var TemplateMapResolver $templateMapResolver */
        $templateMapResolver = $app->getServiceManager()->get('ViewTemplateMapResolver');
        // Create and register layout listener
        $listener = new LayoutListener($templateMapResolver);
        $listener->attach($app->getEventManager());
    }
}
