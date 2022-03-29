<?php

declare(strict_types=1);

namespace Uploader;

use Laminas\Router\Http\Segment;

return [
    'router'          => [
        'routes' => [
            'upload' => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/upload[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults'    => [
                        'controller' => Controller\UploadController::class,
                        'action'     => 'admin-upload',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            Uploader\Uploader::class             => Uploader\UploaderFactory::class,
            Uploader\AdapterPluginManager::class => Uploader\AdapterPluginManagerFactory::class,
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\UploadController::class => Controller\Factory\UploadControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'factories' => [
            Fieldset\UploaderAwareFieldset::class  => Fieldset\Factory\UploaderAwareFieldsetFactory::class,
            Fieldset\UploaderAwareMultiFile::class => Fieldset\Factory\UploaderAwareMultiFileFactory::class,
        ],
    ],
    'upload_manager'  => [
        'role'      => 'admin',
        'privilege' => 'admin.access',
    ],
    'view_manager'    => [
        'template_path_stack' => [
            'uploader' => __DIR__ . '/../view',
        ],
    ],
];
