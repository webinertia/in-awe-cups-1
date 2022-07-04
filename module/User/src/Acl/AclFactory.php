<?php

declare(strict_types=1);

namespace User\Acl;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\OwnershipAssertion as Owner;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Model\Roles;

final class AclFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Acl
    {
        $acl   = new Acl();
        $roles = $container->get(Roles::class);
        $roles = $roles->getRoles();

        $guest = new Role('guest');
        $acl->addRole($guest);

        foreach ($roles as $role) {
            $acl->addRole($role['role'], $role['inheritsFrom']);
        }
        $acl->addRole('superAdmin', 'admin');

        $acl->addResource('admin');
        $acl->addResource('settings', 'admin');
        $acl->addResource('features', 'settings');
        $acl->addResource('roles', 'admin');
        $acl->addResource('server', 'settings');
        $acl->addResource('seo', 'settings');
        $acl->addResource('view', 'settings');
        $acl->addResource('theme', 'settings');

        $acl->addResource('users');
        $acl->addResource('account', 'users');
        $acl->addResource('profile', 'account');
        $acl->addResource('user_list', 'users');

        $acl->addResource('mail');
        $acl->addResource('contact_us', 'mail');
        $acl->addResource('site_message', 'mail');

        $acl->addResource('content');
        $acl->addResource('pages', 'content');

        $acl->allow('guest', 'content', 'view'); // should allow reading of pages
        $acl->allow('guest', 'account', ['register', 'login']); // should allow showing the register, login tabs
        $acl->deny('guest', 'account', 'logout'); // should prevent guest from seeing the logout

        $acl->allow('user', null, ['view', 'edit', 'delete'], new Owner()); // should allow user to view, edit, and delete their own account
        $acl->deny('user', 'account', ['register', 'login']);
        $acl->allow('user', 'account', 'logout');
        $acl->allow('user', 'profile', 'view'); // allow any logged in user to view their profile
        $acl->allow('user', 'profile', ['edit', 'delete'], new Owner());
        $acl->deny(['guest', 'user'], 'admin', 'view'); // should prevent guests and users from seeing the admin page

        $acl->allow('staff', ['account', 'profile', 'content'], ['view', 'create', 'edit', 'delete', 'upload.images']); // should allow staff to view, edit, and delete their own account
        $acl->allow('staff', 'admin', 'view'); // should allow staff to view the admin page
        $acl->deny('staff', ['settings']);
        $acl->allow('admin', null, ['create', 'view', 'edit', 'delete', 'admin.access']); // should allow admin to view, edit, and delete everything
        $acl->allow('admin', 'roles', 'assign'); // should allow admin to assign roles to users
        $acl->allow('superAdmin');

        return $acl;
    }
}
