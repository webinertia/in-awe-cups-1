<?php

declare(strict_types=1);

namespace App\Listener;

use Laminas\Config\Config;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;

final class LayoutVariablesListener extends AbstractListenerAggregate
{
    /** @var array $appSettings */
    protected $appSettings;
    /** @return void */
    public function __construct(array $appSettings = [])
    {
        $this->appSettings = $appSettings;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setLayoutVariables']);
    }

    public function setLayoutVariables(MvcEvent $event): void
    {
        // Get root view model
        $layoutViewModel = $event->getViewModel();
        // Rendering without layout?
        if ($layoutViewModel->terminate()) {
            return;
        }

        // Change template
        $layoutViewModel->setVariables([
            'appSettings' => $this->appSettings,
            'renderPage'  => false,
        ]);
    }
}
