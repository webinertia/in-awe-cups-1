<?php

declare(strict_types=1);

namespace User\Form\Factory;

use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Form\UserForm;
use User\Model\Users;
use User\Permissions\PermissionsManager;

class UserFormFactory implements FactoryInterface
{
    const IDENTITY = 'userName';
    public function __invoke(ContainerInterface $container, $requestedName, $options = [])
    {
        $usrModel = $container->get(Users::class);
        $auth     = $container->get(AuthenticationService::class);
        $usrModel->exchangeArray($auth->hasIdentity() ? $usrModel->fetchByColumn(self::IDENTITY, $auth->getIdentity())->toArray() : $usrModel->fetchGuestContext());
        return new UserForm($auth, $container->get(PermissionsManager::class), $usrModel, $container->get(Settings::class), $options);
    }
}
