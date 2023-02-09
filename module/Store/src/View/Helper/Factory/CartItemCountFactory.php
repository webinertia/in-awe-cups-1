<?php

declare(strict_types=1);

namespace Store\View\Helper\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Model\Cart;
use Store\View\Helper\CartItemCount;

final class CartItemCountFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CartItemCount
    {
        return new $requestedName($container->get(Cart::class));
    }
}
