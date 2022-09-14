<?php

declare(strict_types=1);

namespace App\Listener\Factory;

use App\Listener\MySqlGlobalAdapterInit;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class MySqlGlobalAdapterInitFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): MySqlGlobalAdapterInit {
        return new $requestedName($container->get(AdapterInterface::class));
    }
}
