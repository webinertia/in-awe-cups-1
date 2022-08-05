<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\FormElementManager;

trait FormManagerAwareTrait
{
    /** @var FormElementManager */
    protected $formManager;
    public function setFormManager(FormElementManager $formManager): void
    {
        $this->formManager = $formManager;
    }

    public function getFormManager(): FormElementManager
    {
        return $this->formManager;
    }
}
