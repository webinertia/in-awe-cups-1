<?php

declare(strict_types=1);

namespace Application\Form;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Form;

use function strtolower;

class SettingsForm extends Form
{
    public function __construct($name, $options = [])
    {
        parent::__construct('appSettings');
        parent::setOptions($options);
        $appSettings = $this->getOptions();
        foreach ($appSettings as $setting) {
            foreach ($setting as $data) {
                switch (strtolower($data['settingType'])) {
                    case 'checkbox':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $element = new Checkbox();
                        $element->setName($data['variable']);
                        $element->setValue($data['value']);
                        $element->setLabel($data['label']);
                // $element->setAttribute('class', 'form-control');
                                $element->setLabelAttributes(['class' => 'form-control-sm']);
                //$element->setLabelOption('position', 'top');
                                $this->add($element);

                        break;
                    case 'text':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    $element = new Text();
                        $element->setName($data['variable']);
                        $element->setValue($data['value']);
                        $element->setLabel($data['label']);
                        $element->setAttribute('class', 'form-control');
        //$element->setLabelAttributes(['class' => 'form-control']);
                            //$element->setOption('order', $data['id']);
                            $this->add($element);

                        break;
                    case 'textarea':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    $element = new Textarea();
                        $element->setName($data['variable']);
                        $element->setLabel($data['label']);
                    //$element->setLabelAttributes(['class' => 'form-control']);
                            $element->setValue($data['value']);
                    //$element->setOption('order', $data['id']);
                            $element->setAttribute('class', 'form-control');
                        $this->add($element);

                        break;
                    default:
                        break;
                }
            }
        }
    }
}
