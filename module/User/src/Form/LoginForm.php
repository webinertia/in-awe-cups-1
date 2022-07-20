<?php

declare(strict_types=1);

namespace User\Form;

use App\Form\BaseForm;
use User\Form\Fieldset\LoginFieldset;

class LoginForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct('login');
    }

    public function init(): void
    {
        parent::init();
        $this
        ->add([
            'type' => LoginFieldset::class,
        ]);
    }
}
