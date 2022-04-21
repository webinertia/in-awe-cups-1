<?php

declare(strict_types=1);

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'db'              => [
        'auth_identity_column'   => 'userName',
        'auth_credential_column' => 'password',
        'users_table_name'       => 'users',
        'user_roles_table_name'  => 'user_roles',
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
                    'login'    => [
                        'type'          => Literal::class,
                        'may_terminate' => false,
                        'options'       => [
                            'route'    => '/user/login',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'logout'   => [
                        'type'          => Literal::class,
                        'may_terminate' => false,
                        'options'       => [
                            'route'    => '/user/logout',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'logout',
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
                'resource'  => 'users',
                'privilege' => 'user.view.list',
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
                'route'     => 'user/login',
                'class'     => 'nav-link',
                'action'    => 'login',
                'resource'  => 'users',
                'privilege' => 'login.view',
            ],
            [
                'label'     => 'Logout',
                'route'     => 'user/logout',
                'class'     => 'nav-link',
                'action'    => 'logout',
                'resource'  => 'users',
                'privilege' => 'logout',
            ],
            [
                'label'     => 'Register',
                'route'     => 'user/register',
                'class'     => 'nav-link',
                'action'    => 'index',
                'resource'  => 'users',
                'privilege' => 'register.view',
            ],
        ],
        'admin'  => [
            [
                'label'     => 'Manage Users',
                'route'     => 'admin.user',
                'iconClass' => 'mdi mdi-account-multiple text-primary',
                // 'controller' => 'admin',
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
    'controllers'     => [
        'factories' => [
            Controller\AccountController::class  => Controller\Factory\AccountControllerFactory::class,
            Controller\AdminController::class    => Controller\Factory\AdminControllerFactory::class,
            Controller\PasswordController::class => Controller\Factory\PasswordControllerFactory::class,
            Controller\ProfileController::class  => Controller\Factory\ProfileControllerFactory::class,
            Controller\RegisterController::class => Controller\Factory\RegisterControllerFactory::class,
            Controller\UserController::class     => Controller\Factory\UserControllerFactory::class,
            Controller\WidgetController::class   => Controller\Factory\WidgetControllerFactory::class,
        ],
    ],
    'model_manager'   => [
        'factories' => [
            Model\Users::class      => Model\Factory\UsersFactory::class,
            Model\Roles::class      => Model\Factory\RolesFactory::class,
        ],
    ],
    'service_manager' => [
        'aliases'   => [
            'Acl'                   => Permissions\PermissionsManager::class,
        ],
        'factories' => [
            Permissions\PermissionsManager::class => Permissions\Factory\PermissionsManagerFactory::class,
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
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'userawarecontrol' => View\Helper\UserAwareControl::class,
            'userAwareControl' => View\Helper\UserAwareControl::class,
            'userControl'      => View\Helper\UserAwareControl::class,
        ],
        'factories' => [
            View\Helper\UserAwareControl::class => View\Helper\Factory\UserAwareControlFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
        'template_map'        => [
            'layout/user' => __DIR__ . '/../view/layout/account-dashboard.phtml',
        ],
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
];
