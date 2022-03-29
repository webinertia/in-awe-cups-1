<?php

declare(strict_types=1);

namespace User\Form\Fieldset;

use Laminas\Form\Element\Select;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use User\Model\Roles;

class RoleFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var Roles $roleModel */
    protected $roleModel;
/**
 * @return void
 * @throws InvalidArgumentException
 */
    public function __construct(Roles $roleModel)
    {
        $this->roleModel = $roleModel;
        parent::__construct('role-data');
    }

    public function init()
    {
        $this->add([
            'name'    => 'role',
            'type'    => Select::class,
            'options' => [
                'label'         => 'Assign Group?',
                'value_options' => $this->roleModel->fetchSelectData(),
            ],
        ]);
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
