<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use Psr\Log\LoggerInterface;

use function sprintf;

class AdminListener extends AbstractListenerAggregate
{
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var AbstractAppController $controller */
    protected $controller;
    /** @var LoggerInterface $logger */
    protected $logger;
    /** @var TemplateMapResolver */
    protected $templateMapResolver;
    /** @var Translator $translator */
    protected $translator;
    /** @return void */
    public function __construct(
        LoggerInterface $logger,
        TemplateMapResolver $templateMapResolver,
        Translator $translator,
    ) {
        $this->logger              = $logger;
        $this->templateMapResolver = $templateMapResolver;
        $this->translator          = $translator;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'authorize']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setAdminLayout']);
    }

    public function authorize(MvcEvent $event): void
    {
        // Get and check the route match object
        $this->controller = $event->getTarget();
        // Get and check the parameter for current controller
        if (! $this->controller instanceof AdminControllerInterface) {
            return;
        }
        $user = $this->controller->identity()->getIdentity();

        if (
            ! $this->controller->identity()->hasIdentity() ||
            ! $this->controller->acl()->isAllowed($user, $this->controller, 'view')
        ) {
            $action       = $this->translator->translate('admin_access_action');
            $logMessage   = $this->translator->translate('log_forbidden_known_action_403');
            $flashMessage = $this->translator->translate('forbidden_known_action_403');
            $this->logger->alert(
                sprintf(
                    $logMessage,
                    $action
                )
            );
            $this->controller->flashMessenger()->addErrorMessage(
                sprintf(
                    $flashMessage,
                    $action
                )
            );
            $this->controller->redirect()->toRoute('home');
        }
    }

    public function setAdminLayout(MvcEvent $event): void
    {
         // Get and check the route match object
        $routeMatch        = $event->getRouteMatch();
        $controllerManager = $event->getApplication()->getServiceManager()->get(ControllerManager::class);
        if (! $routeMatch) {
             return;
        }
         // Get and check the parameter for current controller
        $this->controller = $routeMatch->getParam('controller');
        $this->controller = $controllerManager->get($this->controller);
        $name             = 'layout/admin';
        // if this is not an admin controller or if we have already got the layout return
        if (! $this->controller instanceof AdminControllerInterface || $this->templateMapResolver->has($name)) {
             return;
        }
        // Get root view model
        $layoutViewModel = $event->getViewModel();
        // Rendering without layout? This will be the case of all ajax requests
        if ($layoutViewModel->terminate()) {
            return;
        }
        // Change template
        $layoutViewModel->setTemplate($name);
    }
}
