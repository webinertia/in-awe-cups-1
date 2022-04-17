<?php

declare(strict_types=1);

namespace ContentManager\Controller\Factory;

use ContentManager\Controller\ContentController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webinertia\ModelManager\ModelManager;

final class ContentControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ContentController
    {
        return new ContentController($container->get(ModelManager::class));
    }
}