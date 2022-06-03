<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Captcha\Image;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Captcha;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Exception\DomainException;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Db\RecordExists;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use User\Filter\PasswordFilter;

final class ResetPassword extends Form
{
    /**
     * @param string $name
     * @param array $options
     * @return void
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function __construct($name, $options)
    {
        parent::__construct('reset_password');
        parent::setOptions($options);
        $this->table = $this->options['db'];

        $this->add([
            'name' => 'resetTimeStamp',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'validationTimeStamp',
            'type' => 'hidden',
        ]);
        $this->add([
            'name'       => 'email',
            'type'       => Email::class,
            'attributes' => 'form-control',
            'options'    => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name'       => 'password',
            'type'       => Password::class,
            'attributes' => 'form-control',
            'options'    => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name'       => 'conf_password',
            'type'       => Password::class,
            'attributes' => [
                'class' => 'form-control',
            ],
            'options'    => [
                'label' => 'Confirm Password',
            ],
        ]);
        if ($this->options['enableCaptcha']) {
            $this->add([
                'name'       => 'captcha',
                'type'       => Captcha::class,
                'attributes' => [
                    'class' => 'form-control',
                ],
                'options'    => [
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
            'type'       => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }

    public function addInputFilter(): InputFilter
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
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
                    'name'    => RecordExists::class,
                    'options' => [
                        'table'     => $this->table->getTable(),
                        'field'     => 'email',
                        'dbAdapter' => $this->table->getAdapter(),
                        'messages'  => [
                            RecordExists::ERROR_NO_RECORD_FOUND => 'Email not found!!',
                        ],
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name'       => 'password',
            'required'   => true,
            'filters'    => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => PasswordFilter::class],
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
        ]);
        $inputFilter->add([
            'name'       => 'conf_password',
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
                    'name'    => Identical::class,
                    'options' => [
                        'token'    => 'password',
                        'messages' => [
                            Identical::NOT_SAME => 'Passwords are not the same',
                        ],
                    ],
                ],
            ],
        ]);
        return $inputFilter;
    }
}
