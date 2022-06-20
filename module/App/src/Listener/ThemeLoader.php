<?php

declare(strict_types=1);

namespace App\Listener;

use App\Model\Theme;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplatePathStack;

final class ThemeLoader extends AbstractListenerAggregate
{
    /** @return void */
    public function __construct(Theme $theme, TemplatePathStack $stack)
    {
        $this->theme = $theme;
        $this->stack = $stack;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'loadTheme']);
    }

    public function loadTheme(MvcEvent $event): void
    {
        $this->stack->addPaths($this->theme->getThemePaths());
    }
}
