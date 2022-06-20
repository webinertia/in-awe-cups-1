<?php

declare(strict_types=1);

namespace ContentManager\Model\Factory;

use ContentManager\Model\Pages;
use Laminas\Config\Config;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Laminas\Router\RouteStackInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Webinertia\ModelManager\ModelManager;

final class PagesFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Pages
    {
        $config = new Config($container->get('config'));
        $router = $container->get(RouteStackInterface::class);
        $pages  = new Pages(
            $config->db->pages_table_name,
            $container->get(EventManager::class),
            $config->db,
            $container->get(Logger::class)
        );
        $pages->setRouter($router);
        return $pages;
    }
}
