<?php

declare(strict_types=1);

namespace User\View\Helper\Factory;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\View\Helper\AclAwareControl;

final class AclAwareControlFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AclAwareControl
    {
         return new $requestedName($container->get(AclInterface::class));
    }
}
