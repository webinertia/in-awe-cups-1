<?php

declare(strict_types=1);

namespace User\Form;

use User\Form\UserForm;
use Uploader\Fieldset\UploaderAwareFieldset;

class ProfileForm extends UserForm
{
    public function init(): void
    {
        parent::init();
        $this->remove('password-data');
        $this->add([
            'type' => UploaderAwareFieldset::class,
        ]);
    }
}
