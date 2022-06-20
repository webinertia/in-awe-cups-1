<?php

declare(strict_types=1);

namespace App\Controller\Plugin\Factory;

use App\Controller\Plugin\Email as Plugin;
use App\Service\Email;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class EmailFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Plugin
    {
        return new Plugin($container->get(Email::class));
    }
}
