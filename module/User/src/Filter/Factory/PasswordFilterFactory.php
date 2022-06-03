<?php

declare(strict_types=1);

namespace User\Filter\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Filter\PasswordFilter;

final class PasswordFilterFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordFilter
    {
        return new PasswordFilter();
    }
}
