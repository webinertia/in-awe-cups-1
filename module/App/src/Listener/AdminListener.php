<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\AdminControllerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Resolver\TemplateMapResolver;
use Throwable;
use User\Permissions\Exception\PrivilegeException;

class AdminListener extends AbstractListenerAggregate
{
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var ModelManager $modelManager */
    protected $modelManager;
    /** @var TemplateMapResolver */
    private $templateMapResolver;
    /** @return void */
    public function __construct(
        TemplateMapResolver $templateMapResolver
    ) {
        $this->templateMapResolver = $templateMapResolver;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'authorize']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'setAdminLayout']);
    }

    public function authorize(MvcEvent $event)
    {
        // Get and check the route match object
        $routeMatch        = $event->getRouteMatch();
        $sm                = $event->getApplication()->getServiceManager();
        $controllerManager = $sm->get(ControllerManager::class);
        $logger            = $sm->get(Logger::class);
        // Get and check the parameter for current controller
        $controller = $routeMatch->getParam('controller');
        $controller = $controllerManager->get($controller);
        if (! $controller instanceof AdminControllerInterface) {
            return;
        }
        $user = $controller->identity()->getIdentity();
        try {
            if (! $controller->acl()->isAllowed($user, $controller, 'view')) {
                throw new PrivilegeException('You have insufficient privileges to complete request');
            }
        } catch (Throwable $th) {
            $message = $th->getMessage();
            $logger->log(Logger::ERR, $message, $user->getLogData());
            $controller->flashMessenger()->addErrorMessage($message);
            $controller->redirectPrev();
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
        $controller = $routeMatch->getParam('controller');
        $controller = $controllerManager->get($controller);
        $name       = 'layout/admin';
        // if this is not an admin controller or if we have already got the layout return
        if (! $controller instanceof AdminControllerInterface || $this->templateMapResolver->has($name)) {
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
