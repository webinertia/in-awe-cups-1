<?php

declare(strict_types=1);

namespace Application\Form;

use Application\Model\Settings;
use Laminas\Captcha\Image;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;

class ContactForm extends Form implements InputFilterProviderInterface
{
    public function __construct(Settings $settings, $name = null, $options = [])
    {
        $this->appSettings = $settings;
        parent::__construct('contact');
        parent::setOptions($options);
    }

    public function init()
    {
        $this->add([
            'name'    => 'fullName',
            'type'    => Text::class,
            'options' => [
                'label' => 'Full Name',
            ],
        ]);
        $this->add([
            'name'    => 'email',
            'type'    => Email::class,
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name'    => 'message',
            'type'    => Textarea::class,
            'options' => [
                'label' => 'Message',
            ],
        ]);
        if ($this->appSettings->security->enable_captcha) {
            $this->add([
                'name'    => 'captcha',
                'type'    => Captcha::class,
                'options' => [
                    'label'   => 'Rewrite Captcha text:',
                    'captcha' => new Image([
                        'name'           => 'myCaptcha',
                        'messages'       => [
                            'badCaptcha' => 'incorrectly rewritten image text',
                        ],
                        'wordLen'        => 5,
                        'timeout'        => 300,
                        'font'           => $_SERVER['DOCUMENT_ROOT'] . '/fonts/arbli.ttf',
                        'imgDir'         => $_SERVER['DOCUMENT_ROOT'] . '/modules/app/captcha/',
                        'imgUrl'         => '/modules/app/captcha/',
                        'lineNoiseLevel' => 4,
                        'width'          => 200,
                        'height'         => 70,
                    ]),
                ],
            ]);
        }
        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name'       => 'fullName',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'email',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'message',
                'required'   => true,
                'filters'    => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 2000,
                        ],
                    ],
                ],
            ],
        ];
    }
}
