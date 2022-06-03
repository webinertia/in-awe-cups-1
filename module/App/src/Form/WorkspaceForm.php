<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Form;

class WorkspaceForm extends Form
{
    /** @var array $fieldsets */
    protected $fieldsets;
    /**
     * @param mixed $name
     * @param null|array $fieldsets
     * @return void
     */
    public function __construct($name = null, ?array $fieldsets = null)
    {
    }

    public function init(): void
    {
    }
}
