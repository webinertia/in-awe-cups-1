<?php
return [
    'module_settings' => [
        'store' => [
            //'enable_product_bundles' => '1',
            'category' => [
                'home_display_count' => 6,
                'show_images' => 1,
            ],
            'product' => [
                'enable_product_bundles' => true,
                'per_page_count' => 12,
            ],
            'order' => [

            ],
            'search_options' => [
                'price_filtering_enabled' => true,
                'price_step_value' => 15,
            ],
            // Aggregate configuration for uploads
            'upload_settings' => [
                'background_color'   => '#ffffff',
                'max_height'         => '500',
                'max_width'          => '500',
                'modify_on_upload'   => true,
                'allowed_extensions' => [
                    'png', 'jpg', 'jpeg'
                ]
            ],
            // needs check to verify these are in use
            'upload' => [
                'renameUploadConfig' => [
                    'randomize'            => true,
                    'use_upload_extension' => true,
                ],
            ],
            'pagination' => [
                'enabled' => true,
                'items_per_page' => 6,
                'control_partial' => '/store/partials/pagination-control',
            ],
        ],
    ],
];