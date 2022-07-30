<?php

declare(strict_types=1);

namespace ContentManager\Db\Listener;

use ContentManager\Db\Listener\PageGatewayListener;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class PageGatewayListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): PageGatewayListener {
        return new $requestedName($container->get('config')['app_settings']['server']['time_format']);
    }
}
