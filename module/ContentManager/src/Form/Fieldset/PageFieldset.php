<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset;

use App\Form\FormInterface;
use ContentManager\Model\Pages;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

final class PageFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var Pages $model */
    public function __construct(Pages $model, ?array $options = null)
    {
        $this->model = $model;
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
        $this->setObject($this->model);
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
            'name' => 'content',
            'type' => Textarea::class,
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
