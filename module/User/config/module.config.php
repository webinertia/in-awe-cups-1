<?php

declare(strict_types=1);

namespace User;

use App\Controller\Factory\AppControllerFactory;
use Laminas\View\Helper\Navigation;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use User\Navigation\View\PermissionAclDelegatorFactory;
use User\Navigation\View\RoleFromAuthenticationIdentityDelegator;

return [
    'db'              => [
        'auth_identity_column'   => 'userName',
        'auth_credential_column' => 'password',
        'users_table_name'       => 'users',
    ],
    'router'          => [
        'routes' => [
            'user'       => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/user',
                ],
                'child_routes'  => [
                    'list'     => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/user/list[/:page[/:count]]',
                            'constraints' => [
                                'page'  => '[0-9]*',
                                'count' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'list',
                                'page'       => 1,
                                'count'      => 10,
                            ],
                        ],
                    ],
                    'register' => [
                        'type'          => Literal::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/user/register',
                            'defaults' => [
                                'controller' => Controller\RegisterController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'verify'   => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/user/register/verify',
                            'defaults' => [
                                'controller' => Controller\RegisterController::class,
                                'action'     => 'verify',
                            ],
                        ],
                    ],
                    'profile'  => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/user/profile[/:action[/:userName]]',
                            'constraints' => [
                                'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ProfileController::class,
                                'action'     => 'view',
                            ],
                        ],
                    ],
                    'account'  => [
                        'type'          => Placeholder::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route' => '/user/account',
                        ],
                        'child_routes'  => [
                            'dashboard' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/account/dashboard[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'dashboard',
                                    ],
                                ],
                            ],
                            'login'    => [
                                'type'          => Literal::class,
                                'may_terminate' => false,
                                'options'       => [
                                    'route'    => '/user/account/login',
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'login',
                                    ],
                                ],
                            ],
                            'edit'      => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'      => '/user/account/edit[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'edit',
                                    ],
                                ],
                            ],
                            'delete'      => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'      => '/user/account/delete[/:userName]',
                                    'constraints' => [
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'delete',
                                    ],
                                ],
                            ],
                            'logout'   => [
                                'type'          => Literal::class,
                                'may_terminate' => false,
                                'options'       => [
                                    'route'    => '/user/account/logout',
                                    'defaults' => [
                                        'controller' => Controller\AccountController::class,
                                        'action'     => 'logout',
                                    ],
                                ],
                            ],
                            'password'  => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/user/acount/password[/:action[/:step]]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'step'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\PasswordController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'admin.user' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/admin/user',
                ],
                'child_routes'  => [
                    'overview' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'       => '/admin/user[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'widgets'    => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/user/widgets[/:action[/:group[/:page[/:itemsPerPage]]]]',
                    'constraints' => [
                        'action'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'group'        => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'         => '[0-9]+',
                        'itemsPerPage' => '[0-9]+',
                    ],
                    'defaults'    => [
                        'controller' => Controller\WidgetController::class,
                        'action'     => 'list',
                    ],
                ],
            ],
        ],
    ],
    'navigation'      => [
        'default' => [
            [
                'label'     => 'Users',
                'route'     => 'user/list',
                'class'     => 'nav-link',
                'action'    => 'list',
                'resource'  => 'users',
                'privilege' => 'view',
            ],
            [
                'label'     => 'Profile',
                'route'     => 'user/profile',
                'class'     => 'nav-link',
                'action'    => 'view',
                'resource'  => 'users',
                'privilege' => 'view',
            ],
            [
                'label'     => 'Login',
                'route'     => 'user/account/login',
                'class'     => 'nav-link',
                'action'    => 'login',
                'resource'  => 'account',
                'privilege' => 'login',
                'order'     => 1000,
            ],
            [
                'label'     => 'Logout',
                'route'     => 'user/account/logout',
                'class'     => 'nav-link',
                'action'    => 'logout',
                'resource'  => 'account',
                'privilege' => 'logout',
                'order'     => 1000,
            ],
            [
                'label'     => 'Register',
                'route'     => 'user/register',
                'class'     => 'nav-link',
                'action'    => 'index',
                'resource'  => 'account',
                'privilege' => 'register',
                'order'     => 999,
            ],
        ],
        'admin'  => [
            [
                'label'     => 'Manage Users',
                'route'     => 'admin.user',
                'iconClass' => 'mdi mdi-account-multiple text-primary',
                'action'    => 'index',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
            ],
            [
                'label'     => 'Logout',
                'route'     => 'user',
                'iconClass' => 'mdi mdi-logout text-success',
                'action'    => 'logout',
                'resource'  => 'user',
                'privilege' => 'logout',
                'order'     => 100,
            ],
        ],
    ],
    'navigation_helpers' => [
        'delegators' => [
            Navigation::class => [
                PermissionAclDelegatorFactory::class,
                RoleFromAuthenticationIdentityDelegator::class,
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\AccountController::class  => AppControllerFactory::class,
            Controller\AdminController::class    => AppControllerFactory::class,
            Controller\PasswordController::class => AppControllerFactory::class,
            Controller\ProfileController::class  => AppControllerFactory::class,
            Controller\RegisterController::class => AppControllerFactory::class,
            Controller\UserController::class     => AppControllerFactory::class,
            Controller\WidgetController::class   => AppControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'identity'  => Controller\Plugin\Identity::class,
            'acl'       => Controller\Plugin\Acl::class,
        ],
        'factories' => [
            Controller\Plugin\Identity::class => Controller\Plugin\Factory\IdentityFactory::class,
            Controller\Plugin\Acl::class      => Controller\Plugin\Factory\AclFactory::class,
        ]
    ],
    'service_manager' => [
        'aliases'   => [
            Model\Users::class           => Db\UserGateway::class,
            Service\UserInterface::class => Service\UserService::class, // Identity controller plugin requires this alias
            AclInterface::class          => Permissions\PermissionsManager::class, // the navigation helper delegator relies on this alias
        ],
        'factories' => [
            Permissions\PermissionsManager::class => Permissions\Factory\PermissionsManagerFactory::class,
            Db\UserGateway::class                 => Db\Factory\UserGatewayFactory::class,
            Service\UserService::class            => Service\Factory\UserServiceFactory::class,
            Model\Roles::class                    => InvokableFactory::class,
        ],
    ],
    'filters'         => [
        'factories' => [
            Filter\PasswordFilter::class => Filter\Factory\PasswordFilterFactory::class,
        ],
    ],
    'form_elements'   => [
        'factories' => [
            Form\Element\RoleSelect::class        => Form\Element\Factory\RoleSelectFactory::class,
            Form\Fieldset\AcctDataFieldset::class => Form\Fieldset\Factory\AcctDataFieldsetFactory::class,
            Form\Fieldset\LoginFieldset::class    => Form\Fieldset\Factory\LoginFieldsetFactory::class,
            Form\Fieldset\PasswordFieldset::class => Form\Fieldset\Factory\PasswordFieldsetFactory::class,
            Form\Fieldset\ProfileFieldset::class  => Form\Fieldset\Factory\ProfileFieldsetFactory::class,
            Form\Fieldset\RoleFieldset::class     => Form\Fieldset\Factory\RoleFieldsetFactory::class,
            Form\UserForm::class                  => Form\Factory\UserFormFactory::class,
            Form\ProfileForm::class               => Form\Factory\UserFormFactory::class,
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'acl'             => View\Helper\Acl::class,
            'aclawarecontrol' => View\Helper\AclAwareControl::class,
            'aclAwareControl' => View\Helper\AclAwareControl::class,
            'aclControl'      => View\Helper\AclAwareControl::class,
            'identity'        => View\Helper\Identity::class,
        ],
        'factories' => [
            View\Helper\Acl::class             => View\Helper\Factory\AclFactory::class,
            View\Helper\AclAwareControl::class => View\Helper\Factory\AclAwareControlFactory::class,
            View\Helper\Identity::class        => View\Helper\Factory\IdentityFactory::class,
        ],
    ],
    'view_manager'    => [
        'display_forbidden_reason' => true,
        'forbidden_template'       => 'error/403'
    ],
    'widgets'         => [
        'member_list'       => [
            'items_per_page' => 2,
            'display_groups' => 'all',
            'widget_name'    => 'Member List',
        ],
        'admin_member_list' => [
            'items_per_page' => 5,
            'display_groups' => 'admin',
            'widget_name'    => 'Administrators',
        ],
    ],
    'upload_manager' => [
        'user' => [
            'adapter' => TableGatewayAdapter::class,
            // if not using the type configuration below the uploader will use a image_dir key for the directory name to upload too
            //'image_dir' => 'images',
            'type' => [
                'profile' => [
                    'upload_path' => '/user/profile/profileImages',
                ],
            ],
            'db_config' => [
                'table_name' => 'users',
                'image_column' => 'profileImage', // if the record is to be saved this key must be present and must match the key in the columns below
                'columns' => [
                    'profileImage'
                ],
            ],
        ],
    ],
];
