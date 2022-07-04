<?php

declare(strict_types=1);

namespace ContentManager\Db\Listener;

use ContentManager\Db\Listener\InsertUpdateListener;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class InsertUpdateListenerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): InsertUpdateListener {
        $config = $container->get('config');
        return new InsertUpdateListener($config['app_settings']['server']['time_format']);
    }
}
