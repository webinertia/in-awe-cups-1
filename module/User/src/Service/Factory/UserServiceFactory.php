<?php

declare(strict_types=1);

namespace User\Service\Factory;

use ArrayObject;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Model\Users;
use User\Service\UserService;
use Webinertia\ModelManager\ModelManager;

final class UserServiceFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserService
    {
        $authService = $container->get(AuthenticationService::class);
        $userModel   = $container->get(ModelManager::class)->build(Users::class);
        if ($authService->hasIdentity()) {
            $user = $userModel->fetchColumns(
                'userName',
                $authService->getIdentity(),
                $userModel->getContextColumns()
            );
        } else {
            $userModel->exchangeArray($userModel->fetchGuestContext());
            $user = $userModel;
        }
        return new UserService($user->getArrayCopy(), ArrayObject::ARRAY_AS_PROPS);
    }
}
