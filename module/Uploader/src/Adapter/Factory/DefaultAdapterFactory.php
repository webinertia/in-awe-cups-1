<?php

declare(strict_types=1);

namespace Uploader\Adapter\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Uploader\Adapter\DefaultAdapter;

class DefaultAdapterFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DefaultAdapter
    {
        return new DefaultAdapter();
    }
}
