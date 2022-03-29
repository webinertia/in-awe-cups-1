<?php

declare(strict_types=1);

namespace Uploader\Fieldset;

use Laminas\Form\Element\Hidden;
use Laminas\Form\Fieldset;

class UploaderAwareFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('upload-config', $options = null);
        $this->setAttribute('id', 'upload-config');
    }

    public function init()
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
                'value' => '/upload/admin-upload',
            ],
        ]);
    }
}
