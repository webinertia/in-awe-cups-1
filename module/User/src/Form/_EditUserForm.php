<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;

use function is_array;

//use Laminas\Db\Sql\Select as DbSelect;
class EditUserForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        if (is_array($options) && ! empty($options)) {
            parent::setOptions($options);
//var_dump($options);
        }
        // We will ignore the name provided to the constructor
        parent::__construct('EditUserForm');
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'regDate',
            'type' => 'hidden',
        ]);
//         $this->add([
//             'name' => 'userName',
//             'type' => 'text',
//             'options' => [
//                 'label' => 'User Name'
//             ]
//         ]);
        $this->add([
            'name'    => 'email',
            'type'    => 'text',
            'options' => [
                'label' => 'Email',
            ],
        ]);
        if ($this->options['acl']->isAllowed($this->options['user'], $this->options['user'], 'admin.user')) {
            $roles  = $options['rolesTable']->select();
            $result = [];
            foreach ($roles as $role) {
                $result[$role->role] = $role->label;
                continue;
            }
            //var_dump($result);
            $this->add([
                'type'    => Element\Select::class,
                'name'    => 'role',
                'options' => [
                    'label'         => 'Assign role?',
                    'value_options' => $result,
                ],
            ]);
        }
        $this->add([
            'name'    => 'password',
            'type'    => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name'    => 'conf_password',
            'type'    => 'password',
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);
        $this->add([
            'name'       => 'submit',
            'type'       => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
