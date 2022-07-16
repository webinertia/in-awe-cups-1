<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset;

use App\Form\FormInterface;
use ContentManager\Model\Page;
use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

final class PageFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var Mvc $page */
    /** @return void */
    public function __construct(Page $page, ?array $options = null)
    {
        $this->page = $page;
        parent::__construct('page-data');
        $this->setAttribute('id', 'page-data');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init(): void
    {
        $this->setUseAsBaseFieldset(true);
        $this->setHydrator(new ArraySerializableHydrator());
        $this->setObject($this->page);
        if ($this->options['mode'] === FormInterface::EDIT_MODE) {
            $this->add([
                'name' => 'id',
                'type' => Hidden::class,
            ]);
        }
        $this->add([
            'name'    => 'label',
            'type'    => Text::class,
            'options' => [
                'label' => 'Page Label (Will show in the menu)',
            ],
        ])
        ->add([
            'name'    => 'order',
            'type'    => Number::class,
            'options' => [
                'label' => 'Order - The order in which the page will be shown',
            ],
        ])
        ->add([
            'name' => 'content',
            'type' => Textarea::class,
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'id'    => [
                'required' => false,
                'filters'  => [
                    ['name' => ToInt::class],
                ],
            ],
            'order' => [
                'required' => false,
                'filters'  => [
                    ['name' => StringTrim::class],
                    ['name' => ToInt::class],
                    ['name' => ToNull::class],
                ],
            ],
        ];
    }
}
