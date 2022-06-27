<?php

declare(strict_types=1);

namespace App\Model\Factory;

use App\Model\Theme;
use Laminas\Config\Config;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ThemeFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Theme
    {
        $config = new Config($container->get('Config'));
        return new Theme(
            $config->db->theme_table_name,
            $container->get(EventManager::class),
            $config->db,
            $container->get(Logger::class)
        );
    }
}
