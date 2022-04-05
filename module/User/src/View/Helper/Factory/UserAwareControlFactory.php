<?php

declare(strict_types=1);

namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\View\Helper\UserAwareControl;

class UserAwareControlFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UserAwareControl
    {
        // $auth = $container->get(AuthenticationService::class);
        // $user = $container->get(Users::class);
        // if($auth->hasIdentity()) {
        //     $user = $user
        //    $auth->getIdentity();
        // }
        // else {
        //     $user->exchangeArray($user->fetchGuestContext());
        // }
        return new UserAwareControl();
    }
}
