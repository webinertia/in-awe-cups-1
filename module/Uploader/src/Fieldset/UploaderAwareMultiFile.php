<?php

declare(strict_types=1);

namespace Uploader\Fieldset;

use Laminas\Form\Element\File;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\IsImage;

class UploaderAwareMultiFile extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = null, $options = null)
    {
        parent::__construct('uploader-multi-file');
    }

    public function init()
    {
        $this->add([
            'name'       => 'file-upload',
            'type'       => File::class,
            'attributes' => [
                'multiple' => true,
            ],
            'options'    => [
                'messages' => [
                    'To select multiple files please use Ctrl+click while selecting files.',
                ],
                'order'    => 1,
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'file-upload' => [
                'required'   => false,
                'validators' => [
                    ['name' => IsImage::class],
                    [
                        'name'    => ImageSize::class,
                        'options' => [
                            'minWidth'  => 100, // Minimum image width
                            'maxWidth'  => 1000, // Maximum image width
                            'minHeight' => 100, // Minimum image height
                            'maxHeight' => 1000, // Maximum image height
                        ],
                    ],
                ],
            ],
        ];
    }
}
