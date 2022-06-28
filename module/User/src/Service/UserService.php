<?php

declare(strict_types=1);

namespace User\Service;

use App\Model\ModelInterface;
use Laminas\Stdlib\ArrayObject;
use Laminas\Stdlib\ArraySerializableInterface;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use User\Model\Roles;

final class UserService extends ArrayObject implements UserInterface, ModelInterface, ArraySerializableInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'users';
    /** @var Roles $roles */
    protected $roles;
    /**
     * @param array $userData
     * @param int $flags
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(array $userData, Roles $roles, $flags)
    {
        $this->roles = $roles;
        parent::__construct($userData, $flags);
    }

    public function getGroupName(): string
    {
        return $this->roles->getGroupName($this->offsetGet('role'));
    }

    public function getOwnerId(): int
    {
        return (int) $this->offsetGet('id');
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
            'role'      => $this->getGroupName(),
            'firstName' => $this->offsetExists('firstName') ? $this->offsetGet('firstName') : null,
            'lastName'  => $this->offsetExists('lastName') ? $this->offsetGet('lastName') : null,
        ];
    }
}
