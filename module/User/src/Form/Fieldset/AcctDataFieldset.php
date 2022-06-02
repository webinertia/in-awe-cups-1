<?php

declare(strict_types=1);

namespace User\Form\Fieldset;

use App\Form\FormInterface;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\StringLength;

use function array_merge;

class AcctDataFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @param mixed $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct($options = [])
    {
        parent::__construct('acct-data');
        $this->setAttribute('id', 'acct-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function init(): void
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        if ($this->options['mode'] === FormInterface::CREATE_MODE) {
            $this->add([
                'name' => 'regDate',
                'type' => Hidden::class,
            ]);
            $this->add([
                'name'    => 'userName',
                'type'    => Text::class,
                'options' => [
                    'label' => 'User Name',
                ],
            ]);
        }
        $this->add([
            'name'    => 'email',
            'type'    => Text::class,
            'options' => [
                'label' => 'Email',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        $filters = [
            'id'    => [
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
            'email' => [
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
                            'max'      => 320, // true, we may never see an email this length, but they are still valid
                        ],
                    ],
                    // @see EmailAddress for $options
                    ['name' => EmailAddress::class],
                ],
            ],
        ];
        if (isset($this->options['mode']) && $this->options['mode'] === FormInterface::CREATE_MODE) {
            $filter  = [
                'userName' => [
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
            ];
            $filters = array_merge($filters, $filter);
        }
        return $filters;
    }
}
