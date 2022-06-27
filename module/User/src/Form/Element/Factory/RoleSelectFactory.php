<?php

declare(strict_types=1);

namespace User\Form\Element\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Element\RoleSelect;
use User\Model\Roles;

final class RoleSelectFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RoleSelect
    {
        $roles      = $container->get(Roles::class);
        $roleSelect = new RoleSelect('role');
        $roleSelect->setValueOptions($roles->getSelectData());
        return $roleSelect;
    }
}
