<?php

declare(strict_types=1);

namespace User\Form;

use App\Form\BaseForm;
use User\Form\Fieldset\LoginFieldset;

class LoginForm extends BaseForm
{
    /** @param array<mixed> $options */
    public function __construct(string $name = 'login', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        parent::init();
        $this->add([
            'type' => LoginFieldset::class,
            'name' => 'login-data',
        ]);
    }
}
