<?php

declare(strict_types=1);

namespace Uploader\Fieldset;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Uploader\Fieldset\FieldsetInterface;

final class UploaderAwareFieldset extends Fieldset implements InputFilterProviderInterface, FieldsetInterface
{
    /**
     * @param null|array $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(?string $name = null, ?array $options = null)
    {
        parent::__construct('upload-config', $options);
        $this->setAttribute('id', 'upload-config');
        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    public function init(): void
    {
        $this->add([
            'name' => 'module',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'type',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name'       => 'endpoint',
            'type'       => Hidden::class,
            'attributes' => [
                'value' => $this->options['endpoint'] ?? '/upload/admin-upload',
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
