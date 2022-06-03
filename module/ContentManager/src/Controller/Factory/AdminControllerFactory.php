<?php

declare(strict_types=1);

namespace ContentManager\Controller\Factory;

use ContentManager\Controller\AdminController;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Webinertia\ModelManager\ModelManager;

final class AdminControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AdminController
    {
        return new AdminController(
            $container->get(ModelManager::class),
            $container->get(FormElementManager::class)
        );
    }
}
