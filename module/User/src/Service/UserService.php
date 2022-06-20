<?php

declare(strict_types=1);

namespace User\Service;

use Laminas\Stdlib\ArrayObject;
use Laminas\Stdlib\ArraySerializableInterface;

final class UserService extends ArrayObject implements UserInterface, ArraySerializableInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'users';

    public function getOwnerId(): mixed
    {
        return $this->offsetGet('id');
    }

    public function getRoleId(): string
    {
        return $this->offsetGet('role');
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getLogData(): array
    {
        return [
            'userId'    => $this->offsetGet('id'),
            'userName'  => $this->offsetGet('userName'),
            'firstName' => $this->offsetExists('firstName') ? $this->offsetGet('firstName') : null,
            'lastName'  => $this->offsetExists('lastName') ? $this->offsetGet('lastName') : null,
        ];
    }
}
