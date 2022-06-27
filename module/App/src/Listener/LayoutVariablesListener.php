<?php

declare(strict_types=1);

namespace App\Listener;

use App\Model\Settings;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use User\Service\UserInterface;

final class LayoutVariablesListener extends AbstractListenerAggregate
{
    /** @var Settings $appSettings */
    protected $appSettings;
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var ModelManager $modelManager */
    protected $modelManager;
    /** @var Users $user */
    protected $user;
    /** @return void */
    public function __construct(UserInterface $userInterface, Settings $appSettings)
    {
        $this->user        = $userInterface;
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
            'user'        => $this->user,
            'renderPage'  => false,
        ]);
    }
}
