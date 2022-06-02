<?php

declare(strict_types=1);

namespace App\Form\Fieldset;

use App\Form\FormInterface;
use Laminas\Captcha\Image;
use Laminas\Config\Config;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class SecurityFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param string $name
     * @param mixed $options
     * @throws InvalidArgumentException
     */
    public function __construct(Config $appSettings, $options = [])
    {
        //$options['mode'] = 'create';
        $this->appSettings = $appSettings;
        parent::__construct('security-data');
        //$this->setAttribute('id', 'security-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init(): void
    {
        $this->add(
            [
                'name' => 'security_token',
                'type' => Csrf::class,
            ],
            [
                'priority' => 101,
            ]
        );
        if ($this->appSettings->security->enable_captcha && $this->options['mode'] === FormInterface::CREATE_MODE) {
            $this->add(
                [
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
                ],
                [
                    'priority' => -99,
                ]
            );
        }
    }

    public function getInputFilterSpecification(): array
    {
        return [
            // 'security_token' => [
            //     'validators' => [
            //         [
            //             'name' => CsrfValidator::class,
            //         ],
            //     ],
            // ],
        ];
    }
}
