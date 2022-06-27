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

final class PermissionsManager implements AclInterface
{
    /** @var Laminas\Config\Config $config */
    protected $config;
    /** @var Acl $acl */
    public $acl;
    /** @var Roles $model */
    protected $model;
    /** @var array $roles */
    private $roles;
    /** @return $this */
    public function __construct(Acl $acl, Roles $model, Config $config)
    {
        $this->config = $config;
        $this->model  = $model;
        $this->roles  = $this->model->getRoles();
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
        // $user = new Role('user');
        // $this->acl->addRole($user, [$guest]);
        // $staff = new Role('staff');
        // $this->acl->addRole($staff, [$guest, $user]);
        // $admin = new Role('admin');
        // $this->acl->addRole($admin, [$guest, $user, $staff]);
        // $this->acl->addRole(new Role('superAdmin'), [$guest, $user, $staff, $admin]);

        foreach ($this->roles as $role) {
            $this->acl->addRole($role['role'], $role['inheritsFrom']);
        }
        $this->acl->addRole('superAdmin', 'admin');
        /**
         * Permission schema:
         * $resource.$controller.$action.$subactions
         *
         *
         *
         * users.account.edit, users.account.delete, users.account.login
         * users.register, users.verify
         * users.profile.edit, users.profile.view
         */
        $this->acl->addResource('admin');
        $this->acl->addResource('settings', 'admin');
        $this->acl->addResource('features', 'settings');
        $this->acl->addResource('roles', 'admin');
        $this->acl->addResource('server', 'settings');
        $this->acl->addResource('seo', 'settings');
        $this->acl->addResource('view', 'settings');
        $this->acl->addResource('theme', 'settings');

        $this->acl->addResource('users');
        $this->acl->addResource('account', 'users');
        $this->acl->addResource('profile', 'account');
        $this->acl->addResource('user_list', 'users');

        $this->acl->addResource('mail');
        $this->acl->addResource('contact_us', 'mail');
        $this->acl->addResource('site_message', 'mail');

        $this->acl->addResource('content');
        $this->acl->addResource('pages', 'content');

        $this->acl->allow('guest', 'content', 'view'); // should allow reading of pages
        $this->acl->allow('guest', 'account', ['register', 'login']); // should allow showing the register, login tabs
        $this->acl->deny('guest', 'account', 'logout'); // should prevent guest from seeing the logout

        $this->acl->allow('user', null, ['view', 'edit', 'delete'], new Owner()); // should allow user to view, edit, and delete their own account
        $this->acl->deny('user', 'account', ['register', 'login']);
        $this->acl->allow('user', 'account', 'logout');
        $this->acl->allow('user', 'profile', 'view'); // allow any logged in user to view their profile
        $this->acl->allow('user', 'profile', ['edit', 'delete'], new Owner());
        $this->acl->deny(['guest', 'user'], 'admin', 'view'); // should prevent guests and users from seeing the admin page

        $this->acl->allow('staff', ['account', 'profile', 'content'], ['view', 'edit', 'delete']); // should allow staff to view, edit, and delete their own account
        $this->acl->allow('staff', 'admin', 'view'); // should allow staff to view the admin page
        $this->acl->deny('staff', ['settings']);
        $this->acl->allow('admin', null, ['create', 'view', 'edit', 'delete', 'admin.access']); // should allow admin to view, edit, and delete everything

        $this->acl->allow('superAdmin');

        return $this;
    }

    public function getRoles(): array
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
