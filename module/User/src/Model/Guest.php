<?php

declare(strict_types=1);

namespace User\Model;

use App\Model\ModelInterface;

use function strtolower;

final class Guest implements ModelInterface
{
    /** @var int $userId */
    protected $id = 0;
    /** @var string $role */
    protected $role = 'Guest';
    /** @var string $label */
    protected $label = 'Guest';
    /** @var string $inheritsFrom */
    protected $inheritsFrom;
    /** @var string $userName */
    protected $userName = 'Guest';
    /** @var string $firstName */
    protected $firstName = 'Guest';
    /** @var string $lastName */
    protected $lastName;
    /** @var string $resourceId */
    protected $resourceId = 'Guest';

    public function getResourceId(): string
    {
        return strtolower($this->resourceId);
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'role'         => $this->role,
            'label'        => $this->label,
            'inheritsFrom' => $this->inheritsFrom,
            'userName'     => $this->userName,
            'firstName'    => $this->firstName,
            'lastName'     => $this->lastName,
        ];
    }

    public function getArrayCopy(): array
    {
        return $this->toArray();
    }
}
