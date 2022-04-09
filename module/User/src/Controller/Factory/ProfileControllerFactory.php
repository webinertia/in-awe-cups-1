<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\ProfileController;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class ProfileControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProfileController
    {
        return new ProfileController(($container->get(ModelManager::class))->get(Users::class));
    }
}
