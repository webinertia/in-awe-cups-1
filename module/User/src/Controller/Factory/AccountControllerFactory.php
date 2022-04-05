<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\AccountController;
use Webinertia\ModelManager\ModelManager;

final class AccountControllerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AccountController
    {
        $modelManager = $container->get(ModelManager::class);
        $formManager  = $container->get(FormElementManager::class);
        return new AccountController($modelManager, $formManager);
    }
}
