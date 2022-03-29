<?php

declare(strict_types=1);

return [
    'app_settings' => [
        'seo'      => [
            'seo_key_words'   => '',
            'seo_description' => '',
        ],
        'view'     => [
            'site_name'      => '',
            'copyright_link' => '',
            'copyright_text' => '',
            'footer_text'    => '',
        ],
        'email'    => [
            'contact_form_email'   => '',
            'smtp_sender_address'  => '',
            'smtp_sender_password' => '',
            'enable_contact_form'  => true,
        ],
        'security' => [
            'enable_login'          => true, // only disable this in case of emergency and if you have database access
            'enable_registration'   => true,
            'enable_captcha'        => true,
            'recaptcha_private_key' => '',
            'recaptcha_public_key'  => '',
        ],
        'server'   => [
            'host'                 => '',
            'request_scheme'       => '',
            'time_format'          => 'm-j-Y g:i:s',
            'time_zone'            => 'America/Chicago',
            'enable_error_log'     => true,
            'enable_firebug_debug' => true,
            'enable_translation'   => false,
        ],
    ],
];
