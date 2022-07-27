<?php

declare(strict_types=1);

namespace User\Form\Fieldset;

use App\Form\FormInterface;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Filter\ToInt;

final class SocialMediaFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(?string $name = null, ?array $options = [], ?array $appSettings = [])
    {
        if ($name === null) {
            $name = 'social-media';
        }
        if ($options === [] || $options === null) {
            $options = [
                'mode' => FormInterface::EDIT_MODE,
            ];
        }
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        parent::init();
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ])->add([
            'name' => 'userName',
            'type' => Hidden::class,
        ])->add([
            'name'    => 'facebook',
            'type'    => Text::class,
            'options' => [
                'label' => 'Facebook',
            ],
        ])->add([
            'name'    => 'twitter',
            'type'    => Text::class,
            'options' => [
                'label' => 'Twitter',
            ],
        ])->add([
            'name'    => 'instagram',
            'type'    => Text::class,
            'options' => [
                'label' => 'Instagram',
            ],
        ])->add([
            'name'    => 'slack',
            'type'    => Text::class,
            'options' => [
                'label' => 'Youtube',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [
            'id' => [
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ],
        ];
    }
}
