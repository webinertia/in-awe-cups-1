<?php

/**
 * based on code by https://github.com/froschdesign
 */

declare(strict_types=1);

namespace User\Navigation\View;

use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\Navigation\AbstractHelper;
use Psr\Container\ContainerInterface;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class RoleFromAuthenticationIdentityDelegator
{
    /** @var string */
    private $authenticationServiceName;

    /** @var string */
    private $getRoleMethodName;

    public function __construct(
        string $authenticationServiceName = AuthenticationService::class,
        string $getRoleMethodName = 'getRoleId'
    ) {
        $this->authenticationServiceName = $authenticationServiceName;
        $this->getRoleMethodName         = $getRoleMethodName;
    }

    public static function __set_state(array $state): self
    {
        return new self(
            $state['authenticationServiceName'] ??
            AuthenticationService::class,
            $state['getRoleMethodName'] ?? 'getRoleId',
        );
    }

    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        array $options = null
    ) {
        $helper = $callback();

        if (! $helper instanceof AbstractHelper) {
            return $helper;
        }

        if (! $container->has($this->authenticationServiceName)) {
            return $helper;
        }

        $authenticationService = $container->get(
            $this->authenticationServiceName
        );
        if (! $authenticationService instanceof AuthenticationService) {
            return $helper;
        }
        $modelManager = $container->get(ModelManager::class);
        $user         = $modelManager->get(Users::class);
        switch ($authenticationService->hasIdentity()) {
            case true:
                $user = $user->fetchColumns(
                    'userName',
                    $authenticationService->getIdentity(),
                    $user->getContextColumns()
                );
                break;
            case false:
            default:
                $user->exchangeArray($user->fetchGuestContext());
                break;
        }
        $helper->setRole($user);
        return $helper;
    }
}
