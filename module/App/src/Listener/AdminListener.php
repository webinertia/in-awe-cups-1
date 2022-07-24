<?php

declare(strict_types=1);

namespace App\Listener;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\Exception\RuntimeException;
use Laminas\View\Resolver\TemplateMapResolver;
use Throwable;

class AdminListener extends AbstractListenerAggregate
{
    /** @var AuthenticationService $authService */
    protected $authService;
    /** @var AbstractAppController $controller */
    protected $controller;
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

    public function authorize(MvcEvent $event): void
    {
        // Get and check the route match object
        $this->controller = $event->getTarget();
        $logger           = $this->controller->getLogger();
        // Get and check the parameter for current controller
        if (! $this->controller instanceof AdminControllerInterface) {
            return;
        }
        $user = $this->controller->identity()->getIdentity();
        try {
            if (
                ! $this->controller->identity()->hasIdentity() ||
                ! $this->controller->acl()->isAllowed($user, $this->controller, 'view')
            ) {
                throw new RuntimeException('You have insufficient privileges to complete request');
            }
        } catch (Throwable $th) {
            $message = $th->getMessage();
            $logger->notice($th->getMessage());
            $this->controller->flashMessenger()->addErrorMessage($message);
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
