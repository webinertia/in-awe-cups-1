<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Form\Fieldset\RoleFieldset;
use User\Model\Roles;

class RoleFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new RoleFieldset($container->get(Roles::class));
    }
}
