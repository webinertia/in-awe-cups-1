<?php

declare(strict_types=1);

namespace User\Permissions;

use Laminas\Config\Config;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Permissions\Acl\Assertion\OwnershipAssertion as Owner;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Role\RoleInterface;
use User\Model\Roles;

class PermissionsManager implements AclInterface
{
    /** @var Laminas\Config\Config $config */
    protected $config;
    /** @var Acl $acl */
    public $acl;
    /** @var Roles $model */
    protected $model;
    /** @var ResultSet $roles */
    private $roles;

    public function __construct(Acl $acl, Roles $model, Config $config)
    {
        $this->config = $config;
        $this->model  = $model;
        $this->roles  = $this->model->select();
        $this->acl    = $acl;
        $this->build();
        return $this;
    }

    /**
     * @param RoleInterface|string $role
     * @param ResourceInterface|string $resource
     * @param string $privilege
     */
    public function isAllowed($role = null, $resource = null, $privilege = null): bool
    {
        $acl = $this->getAcl();
        return $acl->isAllowed($role, $resource, $privilege);
    }

    /**
     * @param ResourceInterface|string $resource
     * @param mixed $parent
     */
    public function hasResource($resource, $parent = null): bool
    {
        $acl = $this->getAcl();
        return $acl->hasResource($resource, $parent);
    }

    public function build(): self
    {
        // create the guest role and register it
        $guest = new Role('guest');
        $this->acl->addRole($guest);
        $this->acl->addRole(new Role('users'));
        foreach ($this->roles as $role) {
            $this->acl->addRole($role->role, $role->inheritsFrom);
        }
        $this->acl->addRole(new Role('superAdmin', 'admin'));
        $this->acl->addResource('users');
        $this->acl->addResource('user_profile');
        $this->acl->addResource('admin');
        $this->acl->addResource('settings');
        $this->acl->addResource('mailService');
        $this->acl->addResource('pages');
        $this->acl->allow('guest', null, 'view');
        $this->acl->allow('user', null, 'view');
        $this->acl->allow('guest', 'users', ['register.view', 'login.view']);
        $this->acl->allow('user', 'users', 'logout');
        $this->acl->allow('user', 'users', 'user.view.list');
        $this->acl->deny('user', 'users', ['register', 'login', 'user.create.new']);
        $this->acl->deny(['guest', 'users'], 'admin', 'admin.access');
        $this->acl->allow('user', null, ['edit', 'delete'], new Owner());
        //$this->acl->allow('user', 'user', 'edit', new Owner());
        $this->acl->allow('user', 'user_profile', 'edit', new Owner());
        //$this->acl->allow('user', 'project', 'edit', new Owner());
        $this->acl->allow('admin', 'admin', ['admin.access', 'admin.settings', 'admin.user']);
        $this->acl->allow('admin');
        //$this->acl->deny('admin', 'user', ['register', 'login']);
        $this->acl->deny('admin', 'admin', 'admin.add.setting');
        $this->acl->allow('superAdmin');
        //$this->acl->deny(['admin', 'superAdmin'], 'user', ['register.view', 'login.view']);

        return $this;
    }

    public function getRoles(): object
    {
        return $this->roles;
    }

    public function getAcl(): Acl
    {
        return $this->acl;
    }

    /**
     * @param Acl $acl
     */
    public function setAcl($acl): void
    {
        $this->acl = $acl;
    }
}
