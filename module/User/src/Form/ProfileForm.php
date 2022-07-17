<?php

declare(strict_types=1);

namespace User\Form;

use User\Form\UserForm;

class ProfileForm extends UserForm
{
    public function init(): void
    {
        parent::init();
        $options = $this->getOptions();
        $factory = $this->getFormFactory();
        $manager = $factory->getFormElementManager();
        $this->remove('password-data');
    }
}
