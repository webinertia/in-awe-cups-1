<?php

declare(strict_types=1);

namespace User\Model;

use App\Model\ModelInterface;
use Laminas\Config\Factory;

use function dirname;

final class Roles implements ModelInterface
{
    /** @var array $roleData */
    protected $selectData = [];
    /** @var array $roles */
    protected $roles = [];
    /** @var string $configFilename */
    protected $configFilename = 'roles.json';

    public function __construct()
    {
        $this->fileName = dirname(__DIR__, 4) . '/config/roles.php';
        $this->config   = Factory::fromFile($this->fileName);
        $this->processConfig($this->config);
    }

    protected function processConfig(array $config)
    {
        $selectData = [];
        $roleData   = [];
        foreach ($config as $role) {
            $roleData[] = $role;
            // The following builds the select data array for the roles dropdown form element
            $selectData[$role['id']] = [
                'value' => $role['role'],
                'label' => $role['label'],
            ];
        }
        $this->setRoles($roleData);
        $this->setSelectData($selectData);
    }

    /**
     * @param string $role
     */
    public function getRoleData($role): array
    {
        return $this->roles[$role];
    }

    public function getGroupName(string $role): string
    {
        return $this->config[$role]['label'];
    }

    /** @param array $selectData */
    protected function setSelectData($selectData): void
    {
        $this->selectData = $selectData;
    }

    public function getSelectData(): array
    {
        return $this->selectData;
    }

    /** @param array $roles */
    protected function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getResourceId(): string
    {
        return 'roles';
    }
}
