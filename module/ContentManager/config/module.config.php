<?php

declare(strict_types=1);

namespace ContentManager;

use App\Controller\Factory\AppControllerFactory;
use ContentManager\Navigation\Service\DefaultNavigationFactory;
use Laminas\Navigation\Navigation;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'db'                => [
        'pages_table_name' => 'pages',
    ],
    'page_upload_paths' => [
        'local_path'  => '/public/modules/contentmanager/page/content/images/',
        'public_path' => '/modules/contentmanager/page/content/images/',
    ],
    'router'            => [
        'routes' => [
            'page'          => [
                'type'          => Segment::class,
                'may_terminate' => true,
                'options'       => [
                    'route'       => '[/:title]',
                    'constraints' => [
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults'    => [
                        'controller' => Controller\ContentController::class,
                        'action'     => 'page',
                    ],
                ],
            ],
            'admin.content' => [
                'type'          => Placeholder::class,
                'may_terminate' => true,
                'options'       => [
                    'route' => '/admin/content',
                ],
                'child_routes'  => [
                    'manager' => [
                        'type'          => Literal::class,
                        'may_terminate' => true,
                        'options'       => [
                            'route'    => '/admin/content/manager',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'dashboard',
                            ],
                        ],
                        'child_routes'  => [
                            'create' => [
                                'type'          => Literal::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/create',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'create',
                                    ],
                                ],
                            ],
                            'edit'   => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/edit[/:title]',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'edit',
                                    ],
                                ],
                            ],
                            'delete' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'       => '/delete[/:id]',
                                    'defaults'    => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'delete',
                                    ],
                                    'constraints' => [
                                        'id' => '[0-9]*',
                                    ],
                                ],
                            ],
                            'upload' => [
                                'type'          => Segment::class,
                                'may_terminate' => true,
                                'options'       => [
                                    'route'    => '/upload',
                                    'defaults' => [
                                        'controller' => Controller\AdminController::class,
                                        'action'     => 'upload-images',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'       => [
        'factories' => [
            Controller\AdminController::class   => AppControllerFactory::class,
            Controller\ContentController::class => AppControllerFactory::class,
        ],
    ],
    'form_elements'     => [
        'factories' => [
            Form\PageForm::class              => Form\Factory\PageFormFactory::class,
            Form\Fieldset\PageFieldset::class => Form\Fieldset\Factory\PageFieldsetFactory::class,
        ],
    ],
    'service_manager'   => [
        'aliases'   => [
            'navigation' => Navigation::class,
        ],
        'factories' => [
            Navigation::class                       => DefaultNavigationFactory::class,
            Db\PageGateway::class                   => Db\Factory\PageGatewayFactory::class,
            Db\Listener\InsertUpdateListener::class => Db\Listener\InsertUpdateListenerFactory::class,
        ],
    ],
    'navigation'        => [
        'admin' => [
            [
                'label'     => 'Page Manager',
                'uri'       => '/admin/content',
                'iconClass' => 'mdi mdi-page-layout-body text-warning',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
                'pages'     => [
                    [
                        'label'     => 'Content Dashboard',
                        'route'     => 'admin.content/manager',
                        'action'    => 'dashboard',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                    ],
                    [
                        'label'     => 'Create New Page',
                        'route'     => 'admin.content/manager/create',
                        'action'    => 'create',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                    ],
                ],
            ],
        ],
    ],
];
