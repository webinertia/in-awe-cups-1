<?php

declare(strict_types=1);

namespace User\Service\Factory;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Db\UserGateway;
use User\Service\UserService;

final class UserServiceFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserService
    {
        $authService = $container->get(AuthenticationService::class);
        $userGateway = $container->get(UserGateway::class);
        if ($authService->hasIdentity()) {
            $user = $userGateway->fetchColumns(
                'userName',
                $authService->getIdentity(),
                $userGateway->getContextColumns()
            );
        } else {
            $user = $userGateway->fetchGuestContext();
        }
        $user->setGateway($userGateway);
        return $user;
    }
}
