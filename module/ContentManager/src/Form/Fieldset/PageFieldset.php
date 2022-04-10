<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\InputFilter\InputFilterProviderInterface;

final class PageFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(?array $options = null)
    {
        parent::__construct('page-data');
        $this->setAttribute('id', 'page-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init()
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ])
        ->add([
            'name' => 'label',
            'type' => Text::class,
            'options' => [
                'label' => 'Page Label (Will show in the menu)',
            ],
        ])
        ->add([
            'name' => 'content',
            'type' => Textarea::class,
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
