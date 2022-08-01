<?php

declare(strict_types=1);

namespace User\Controller\Plugin\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Controller\Plugin\Identity;
use User\Service\UserServiceInterface;

final class IdentityFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Identity
    {
        return new Identity(
            $container->get(AuthenticationService::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
