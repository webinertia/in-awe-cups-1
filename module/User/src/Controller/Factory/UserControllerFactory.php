<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\UserController;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class UserControllerFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserController
    {
        $modelManager = $container->get(ModelManager::class);
        return new UserController($modelManager->get(Users::class));
    }
}
