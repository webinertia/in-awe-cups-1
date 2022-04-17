<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\PasswordController;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class PasswordControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordController
    {
        return new PasswordController(($container->get(ModelManager::class))->get(Users::class));
    }
}
