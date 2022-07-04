<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\BaseForm;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Fieldset;
use Laminas\Form\Exception\InvalidArgumentException;

use function gettype;
use function strtolower;

class SettingsForm extends BaseForm
{
    /** @var array $settings */
    protected $settings;
    /**
     * @param array $options
     * @param array $settings
     * @return void
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function __construct($options = [], $settings = [])
    {
        parent::__construct('app_settings', $options);
        parent::setOptions($options);
        $this->settings = $settings;
    }

    public function init()
    {
        foreach ($this->settings as $fieldsetName => $values) {
            $fieldset = new Fieldset($fieldsetName);
            //$fieldset->setLabel($fieldsetName);
            foreach ($values as $elementName => $elementValue) {
                $type = gettype($elementValue);
                switch ($type) {
                    case 'string':
                        $element = new Text($elementName);
                        break;
                    case 'integer':
                        // fallthrough
                    case 'boolean':
                        $element = new Checkbox($elementName);
                        break;
                    default:
                        //throw new InvalidArgumentException('Invalid type for element: ' . $type);
                }
                $element->setLabel($elementName);
                $element->setValue($elementValue);
                $fieldset->add($element);
            }
            $this->add($fieldset);
            continue;
        }
    }
}
