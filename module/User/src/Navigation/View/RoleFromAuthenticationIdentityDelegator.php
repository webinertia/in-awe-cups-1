<?php

/**
 * based on code by https://github.com/froschdesign
 */

declare(strict_types=1);

namespace User\Navigation\View;

use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\Navigation\AbstractHelper;
use Psr\Container\ContainerInterface;
use User\Service\UserInterface;

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
        $user = $container->get(UserInterface::class);
        $helper->setRole($user);
        return $helper;
    }
}
