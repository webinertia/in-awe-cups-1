<?php

declare(strict_types=1);

namespace User\Service;

use App\Model\ModelInterface;
use Laminas\Hydrator\ObjectPropertyHydrator as Hydrator;
use User\Model\Roles;

final class UserService implements UserInterface, ModelInterface
{
    /** @var int $id */
    public $id;
    /** @var string $userName */
    public $userName;
    /** @var string $email */
    public $email;
    /** @var string $password */
    public $password;
    /** @var string $role  */
    public $role;
    /** @var string $groupName */
    protected $groupName;
    /** @var string $firstName */
    public $firstName;
    /** @var string $lastName */
    public $lastName;
    /** @var string $profileImage */
    public $profileImage;
    /** @var string $age */
    public $age;
    /** @var string $birthday */
    public $birthday;
    /** @var string $gender */
    public $gender;
    /** @var string $race */
    public $race;
    /** @var string $bio */
    public $bio;
    /** @var string companyName */
    public $companyName;
    /** @var int $sessionLength */
    public $sessionLength;
    /** @var string regDate */
    public $regDate;
    /** @var int|bool $active */
    public $active;
    /** @var string $prefs_theme */
    public $prefsTheme;
    /** @var string $regHash */
    public $regHash;
    /** @var string resetTimeStamp */
    public $resetTimeStamp;
    /** @var string $resetHash */
    public $resetHash;
    /** @var int $ownerId */
    protected $ownerId;
    /** @var string $resourceId */
    protected $resourceId = 'users';
    /** @var array $logData */
    protected $logData = [];
    /** @var Roles $roleModel */
    protected $roles;

    public function __construct()
    {
        $this->roles = new Roles();
    }

    public function getLogData(): array
    {
        return [
            'userId'    => $this->id,
            'userName'  => $this->userName,
            'role'      => $this->getGroupName(),
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
        ];
    }

    /** @param bool $withPassword */
    public function toArray($withPassword = false): array
    {
        $hydrator = new Hydrator();
        if (! $withPassword && ! empty($this->password)) {
            unset($this->password);
        }
        return $hydrator->extract($this);
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getGroupName(): string
    {
        $this->groupName = $this->roles->getGroupName($this->role);
        return $this->groupName;
    }

    public function getOwnerId(): int
    {
        return (int) $this->id;
    }

    public function getRoleId(): string
    {
        return $this->role;
    }

    public function setResourceId(string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }
}
