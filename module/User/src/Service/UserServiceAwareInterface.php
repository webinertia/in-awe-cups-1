<?php

declare(strict_types=1);

namespace User\Service;

use User\Service\UserServiceInterface;

interface UserServiceAwareInterface
{
    public function setUserService(UserServiceInterface $userService);

    public function getUserService(): UserServiceInterface;
}
