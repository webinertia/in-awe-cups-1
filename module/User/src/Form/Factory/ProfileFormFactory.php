<?php

declare(strict_types=1);

namespace User\Form\Factory;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\ProfileForm;
use User\Service\UserServiceInterface;

final class ProfileFormFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProfileForm
    {
        return new $requestedName(
            $container->get(AclInterface::class),
            $container->get(UserServiceInterface::class),
            $options,
        );
    }
}
