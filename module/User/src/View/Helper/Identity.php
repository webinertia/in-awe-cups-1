<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\Authentication\AuthenticationService;
use Laminas\View\Helper\AbstractHelper;
use User\Service\UserServiceInterface;

final class Identity extends AbstractHelper
{
    /** @var AuthenticationService $authenticationService */
    protected $authenticationService;
    /** @var UserServiceInterface $userInterface */
    protected $userInterface;

    public function __construct(AuthenticationService $authenticationService, UserServiceInterface $userInterface)
    {
        $this->authenticationService = $authenticationService;
        $this->userInterface         = $userInterface;
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments): mixed
    {
        return $this->authenticationService->{$name}(...$arguments);
    }

    public function getIdentity(): UserServiceInterface
    {
        return $this->userInterface;
    }
}
