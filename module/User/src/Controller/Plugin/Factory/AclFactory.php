<?php

declare(strict_types=1);

namespace User\Controller\Plugin\Factory;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Controller\Plugin\Acl;

final class AclFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Acl
    {
        if ($container->has(AclInterface::class)) {
            return new Acl($container->get(AclInterface::class));
        } else {
            throw new ServiceNotCreatedException('Helper requires a registered instance of AclInterface');
        }
    }
}
