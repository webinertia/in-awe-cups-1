<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Form;

class WorkspaceForm extends Form
{
    /** @var array $fieldsets */
    protected $fieldsets;
    /**
     * @param string $name
     * @param null|array $fieldsets
     * @return void
     */
    public function __construct($name = null, ?array $fieldsets = null)
    {
        if ($name === null) {
            $name = 'workSpace';
        }
        parent::__construct($name);
        if ($fieldsets !== null && $fieldsets !== []) {
            $this->fieldsets = $fieldsets;
        }
    }

    public function init(): void
    {
    }
}
