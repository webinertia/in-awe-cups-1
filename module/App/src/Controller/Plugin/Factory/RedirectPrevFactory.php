<?php

declare(strict_types=1);

namespace App\Controller\Plugin\Factory;

use App\Controller\Plugin\RedirectPrev;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;
use Psr\Container\ContainerInterface;

class RedirectPrevFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedirectPrev
    {
        return new RedirectPrev($container->get(Container::class));
    }
}
