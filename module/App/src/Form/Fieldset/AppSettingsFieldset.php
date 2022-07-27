<?php

declare(strict_types=1);

namespace App\Form\Fieldset;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

final class AppSettingsFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param string $name
     * @param array $options
     * @return void
     */
    public function __construct($name = null, $options = [])
    {
        parent::__construct('app_settings');
    }

    public function init(): void
    {
        $server   = new Fieldset('server');
        $log      = new Fieldset('log');
        $seo      = new Fieldset('seo');
        $view     = new Fieldset('view');
        $email    = new Fieldset('email');
        $security = new Fieldset('security');
        $server->add([
            'name'    => 'app_path',
            'type'    => Text::class,
            'options' => ['label' => 'Absolute server path to Application'],
        ])
        ->add([
            'name'    => 'upload_basepath',
            'type'    => Text::class,
            'options' => ['label' => 'Absolute server path to public/module directory'],
        ])
        ->add([
            'name'    => 'request_scheme',
            'type'    => Text::class,
            'options' => ['label' => 'Request Scheme, http or https, set during installation'],
        ])
        ->add([
            'name'    => 'host',
            'type'    => Text::class,
            'options' => ['label' => 'Host, set during installation'],
        ])
        ->add([
            'name'    => 'time_format',
            'type'    => Text::class,
            'options' => ['label' => 'Time Format, should conform to Php Datetime format'],
        ])
        ->add([
            'name'    => 'time_zone',
            'type'    => Text::class,
            'options' => ['label' => 'Time Zone, should conform to Php DateTimeZone format'],
        ])
        ->add([
            'name'    => 'log_errors',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Error Logging?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'enable_translation',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Translation?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'captcha_path',
            'type'    => Text::class,
            'options' => ['label' => 'Absolute server path to captcha directory'],
        ]);
        $log->add([
            'name'    => 'time_format',
            'type'    => Text::class,
            'options' => ['label' => 'Time Format, should conform to Php Datetime format'],
        ]);
        $seo->add([
            'name'    => 'key_words',
            'type'    => Text::class,
            'options' => ['label' => 'Key Words, comma separated'],
        ])
        ->add([
            'name'    => 'description',
            'type'    => Text::class,
            'options' => ['label' => 'Website Description'],
        ]);
        $view->add([
            'name'    => 'site_name',
            'type'    => Text::class,
            'options' => ['label' => 'Site Name'],
        ])
        ->add([
            'name'    => 'copyright_link',
            'type'    => Text::class,
            'options' => ['label' => 'Copyright Link'],
        ])
        ->add([
            'name'    => 'copyright_text',
            'type'    => Text::class,
            'options' => ['label' => 'Copyright Text'],
        ])
        ->add([
            'name'    => 'footer_text',
            'type'    => Text::class,
            'options' => ['label' => 'Footer Text'],
        ])
        ->add([
            'name'    => 'show_breadcrumbs',
            'type'    => Checkbox::class,
            'options' => ['label' => 'Show Breadcrumbs?'],
        ]);
        $email->add([
            'name'    => 'contact_form_email',
            'type'    => Text::class,
            'options' => ['label' => 'Contact Form Address'],
        ])
        ->add([
            'name'    => 'smtp_sender_address',
            'type'    => Text::class,
            'options' => ['label' => 'SMTP Sender Address'],
        ])
        ->add([
            'name'    => 'smtp_sender_password',
            'type'    => Text::class,
            'options' => ['label' => 'SMTP Sender Password'],
        ])
        ->add([
            'name'    => 'enable_contact_form',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Contact Form?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ]);
        $security->add([
            'name'    => 'enable_login',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Member Login?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'enable_registration',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Member Registration?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ])
        ->add([
            'name'    => 'enable_captcha',
            'type'    => Checkbox::class,
            'options' => [
                'label'              => 'Enable Captcha?',
                'use_hidden_element' => true,
                'checked_value'      => '1',
                'unchecked_value'    => '0',
            ],
        ]);
        $this->add($server)
        ->add($log)
        ->add($seo)
        ->add($view)
        ->add($email)
        ->add($security);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'server'   => [
                'app_path'           => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'upload_basepath'    => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'request_scheme'     => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'host'               => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'time_format'        => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'time_zone'          => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'log_errors'         => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_translation' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'captcha_path'       => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'log'      => [
                'time_format' => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'seo'      => [
                'key_words'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'description' => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'view'     => [
                'site_name'        => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'copyright_link'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'copyright_text'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'footer_text'      => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'show_breadcrumbs' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'email'    => [
                'contact_form_email'   => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'smtp_sender_address'  => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'smtp_sender_password' => [
                    'required' => true,
                    'filters'  => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_contact_form'  => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
            'security' => [
                'enable_login'        => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_registration' => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
                'enable_captcha'      => [
                    'filters' => [
                        ['name' => StringTrim::class],
                    ],
                ],
            ],
        ];
    }
}
