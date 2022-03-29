<?php

declare(strict_types=1);

namespace User\Filter\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Filter\PasswordFilter;

class PasswordFilterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new PasswordFilter();
    }
}
