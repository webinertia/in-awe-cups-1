<?php

declare(strict_types=1);

namespace Uploader\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Uploader\Adapter\DefaultAdapter;

class DefaultAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new DefaultAdapter();
    }
}
