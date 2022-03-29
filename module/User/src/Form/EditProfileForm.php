<?php

declare(strict_types=1);

namespace User\Form;

// filters
use Laminas\Form\Element\DateSelect;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;

class EditProfileForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct('EditProfileForm', $options);
        parent::setOptions($options);
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'userId',
            'type' => 'hidden',
        ]);
        $firstName = new Text();
        $firstName->setName('firstName')
                  ->setLabel('First Name')
                  ->setAttribute('class', $this->elementCLass);
        $this->add($firstName);
        $lastName = new Text();
        $lastName->setName('lastName')
                 ->setLabel('Last Name')
                 ->setAttribute('class', $this->elementCLass);
        $this->add($lastName);
        $age = new Number();
        $age->setName('age')
            ->setLabel('Your Age')
            ->setAttribute('class', $this->elementCLass);
        $this->add($age);
        $bday = new DateSelect();
        $bday->setName('birthday')
             ->setLabel('Birthday')
             ->setAttribute('class', $this->elementCLass);
        $this->add($bday);
        $gender = new Select();
        $gender->setName('gender')
               ->setLabel('Gender')
               ->setAttribute('class', $this->elementCLass)
               ->setValueOptions([
                   'male'   => 'Male',
                   'female' => 'Female',
               ]);
        $bio = new Textarea();
        $bio->setName('bio')
            ->setLabel('Biography')
            ->setAttribute('class', $this->elementCLass);
        $file = new File('profileImage');
        $file->setAttribute('id', 'profileImage');
        $file->setLabel('Profile Picture');
        $this->add($file);
        $this->add([
            'name'       => 'submit',
            'label'      => 'Submit',
            'type'       => 'submit',
            'attributes' => [
                'value' => 'Submit',
                'id'    => 'submitbutton',
                'class' => $this->buttonClass,
            ],
        ]);
    }
}
