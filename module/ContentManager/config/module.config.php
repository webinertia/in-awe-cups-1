<?php

declare(strict_types=1);

namespace ContentManager;

use App\Controller\Factory\AppControllerFactory;
use ContentManager\Controller\AdminController;
use ContentManager\Controller\ContentController;
use ContentManager\Form\Fieldset\Factory\PageFieldsetFactory;
use ContentManager\Form\Fieldset\PageFieldset;
use ContentManager\Form\PageForm;
use ContentManager\Form\Factory\PageFormFactory;
use ContentManager\Model\Factory\PagesFactory;
use ContentManager\Model\Pages;
use ContentManager\Navigation\Service\DefaultNavigationFactory;
use Laminas\Navigation\Navigation;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'db' => [
        'pages_table_name' => 'pages',
    ],
    'router' => [
        'routes' => [
            'page' => [
                'type' => Segment::class,
                'may_terminate' => true,
                'options' => [
                    'route' => '[/:title]',
                    'constraints' => [
                        'title'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => ContentController::class,
                        'action'     => 'page',
                    ],
                ],
            ],
            'admin.content' => [
                'type' => Placeholder::class,
                'may_terminate' => true,
                'options' => [
                    'route' => '/admin/content',
                ],
                'child_routes' => [
                    'manager' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/admin/content[/:action[/:title]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'title'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => AdminController::class,
                                'action'     => 'dashboard',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AdminController::class   => AppControllerFactory::class,
            ContentController::class => AppControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            PageForm::class     => PageFormFactory::class,
            PageFieldset::class => PageFieldsetFactory::class,
        ],
    ],
    'model_manager' => [
        'factories' => [
            Pages::class => PagesFactory::class,
        ],
    ],
    'navigation' => [
        'admin'  => [
            [
                'label'     => 'Page Manager',
                'uri'       => '/admin/content',
                'iconClass' => 'mdi mdi-page-layout-body text-warning',
                'resource'  => 'admin',
                'privilege' => 'admin.access',
                'pages' => [
                    [
                        'label'     => 'Content Dashboard',
                        'route'     => 'admin.content/manager',
                        'action'    => 'dashboard',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                        //'params'    => ['action' => 'dashboard']
                    ],
                    [
                        'label'     => 'Create New Page',
                        'route'     => 'admin.content/manager',
                        'action'    => 'create',
                        'resource'  => 'admin',
                        'privilege' => 'admin.access',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'navigation' => Navigation::class,
        ],
        'factories' => [
            Navigation::class => DefaultNavigationFactory::class,
        ],
    ],
];