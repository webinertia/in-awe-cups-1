<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use User\Controller\PasswordController;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class PasswordControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordController
    {
        return new PasswordController(($container->get(ModelManager::class))->get(Users::class));
    }
}
