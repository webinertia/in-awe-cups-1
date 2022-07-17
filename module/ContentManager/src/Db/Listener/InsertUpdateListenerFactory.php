<?php

declare(strict_types=1);

namespace ContentManager\Db\Listener;

use ContentManager\Db\Listener\InsertUpdateListener;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class InsertUpdateListenerFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): InsertUpdateListener {
        return new InsertUpdateListener($container->get('config')['app_settings']['server']['time_format']);
    }
}
