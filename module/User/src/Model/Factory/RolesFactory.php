<?php

declare(strict_types=1);

namespace User\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Roles;

final class RolesFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Roles
    {
        $roles = $container->get(Roles::class);
        $roles->loadRoles();
        return new Roles();
    }
}
