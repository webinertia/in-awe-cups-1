<?php

declare(strict_types=1);

// @codingStandardsIgnoreStart
return [
    'app_settings' => [
        'log'      => [
            'time_format' => 'm-d-Y H:i:s',
        ],
        'seo'      => [
            'key_words'   => 'Aurora CMS, Webinertia.net, Laminas, Php, MySQL',
            'description' => 'Aurora CMS',
        ],
        'view'     => [
            'site_name'        => 'Aurora CMS',
            'copyright_link'   => 'http://webinertia.net/aurora',
            'copyright_text'   => 'Aurora CMS',
            'footer_text'      => 'Developed by Webinertia',
            'show_breadcrumbs' => 0,
        ],
        'email'    => [
            'contact_form_email'   => '',
            'smtp_sender_address'  => '',
            'smtp_sender_password' => '',
            'enable_contact_form'  => 1,
        ],
        'security' => [
            'enable_login'          => 1, // only disable this in case of emergency and if you have database access
            'enable_registration'   => 1,
            'enable_captcha'        => 1,
            'recaptcha_private_key' => '',
            'recaptcha_public_key'  => '',
        ],
        'server'   => [
            'host'               => '',
            'request_scheme'     => '',
            'time_format'        => 'm-j-Y g:i:s',
            'time_zone'          => 'America/Chicago',
            'log_errors'         => 1,
            'enable_translation' => 0,
        ],
    ],
];
// @codingStandardsIgnoreEnd
