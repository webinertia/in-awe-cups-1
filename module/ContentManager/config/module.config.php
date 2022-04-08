<?php

declare(strict_types=1);

namespace ContentManager;

use ContentManager\Controller\ContentController;
use ContentManager\Controller\Factory\ContentControllerFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'content' => [
                'type' => Placeholder::class,
                'may_terminate' => true,
                'child_routes' => [
                    'category' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '[/:parentTitle]',
                            'constraints' => [
                                'parentTitle' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => ContentController::class,
                                'action'     => 'page',
                            ],
                        ],
                    ],
                    'page' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '[/:parentTitle[/:title[/:page]]]',
                            'constraints' => [
                                'parentTitle'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'title'    => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'page'    => '[0-9]',
                            ],
                            'defaults' => [
                                'controller' => ContentController::class,
                                'action'     => 'page',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            ContentController::class => ContentControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'content-manager' => __DIR__ . '/../view'
        ],
    ],
];