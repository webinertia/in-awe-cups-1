<?php

declare(strict_types=1);

namespace App\Listener;

use App\Model\Settings;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use User\Model\Users;
use User\Permissions\PermissionsManager;
use Webinertia\ModelManager\ModelManager;

final class LayoutVariablesListener extends AbstractListenerAggregate
{
    /** @var PermissionsManager $acl */
    protected $acl;
    /** @var Settings $appSettings */
    protected $appSettings;
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var ModelManager $modelManager */
    protected $modelManager;
    /** @var Users $user */
    protected $user;
    /** @return void */
    public function __construct(
        AuthenticationService $authService,
        ModelManager $modelManager
    ) {
        $this->authService  = $authService;
        $this->modelManager = $modelManager;
        $this->appSettings  = $this->modelManager->get(Settings::class);
        $this->user         = $this->modelManager->get(Users::class);
        switch ($this->authService->hasIdentity()) {
            case true:
                $this->user = $this->user->fetchColumns(
                    'userName',
                    $this->authService->getIdentity(),
                    $this->user->getContextColumns()
                );
                break;
            case false:
            default:
                $this->user->exchangeArray($this->user->fetchGuestContext());
                break;
        }
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
            'authService' => $this->authService,
            'user'        => $this->user,
            'renderPage'  => false,
        ]);
    }
}
