<?php

declare(strict_types=1);

namespace User\Controller\Plugin\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Controller\Plugin\LoadUser;
use User\Service\UserInterface;

final class LoadUserFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoadUser
    {
        return new LoadUser($container->get(UserInterface::class));
    }
}
