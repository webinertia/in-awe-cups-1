<?php

declare(strict_types=1);

namespace User\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\ArrayObject;
use Psr\Container\ContainerInterface;
use User\Model\Roles;
use Webinertia\ModelManager\ModelManager;

final class RolesFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Roles
    {
        $roles = $container->get(ModelManager::class)->get(Roles::class);
        $roles->loadRoles();
        return new Roles([], ArrayObject::ARRAY_AS_PROPS);
    }
}
