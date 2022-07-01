<?php

declare(strict_types=1);

namespace User\Form\Fieldset;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Password;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;
use User\Filter\PasswordFilter;

final class PasswordFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param mixed $name
     * @param mixed $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct($name = null, $options = null)
    {
        parent::__construct('password-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init(): void
    {
        $this->add([
            'name'    => 'password',
            'type'    => Password::class,
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name'    => 'conf_password',
            'type'    => Password::class,
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
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
            ],
            [
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
            ],
        ];
    }
}
