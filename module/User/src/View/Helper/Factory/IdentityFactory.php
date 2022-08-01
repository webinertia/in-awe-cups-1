<?php

declare(strict_types=1);

namespace User\View\Helper\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use User\View\Helper\Identity;

final class IdentityFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Identity
    {
        return new $requestedName(
            $container->get(AuthenticationService::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
