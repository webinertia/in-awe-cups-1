<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\BaseForm;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\InvalidArgumentException;
use Laminas\Form\Exception\InvalidElementException;
use Laminas\Form\Fieldset;

use function gettype;

class SettingsForm extends BaseForm
{
    /** @var array $settings */
    protected $settings;
    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [], array $settings = [])
    {
        if ($settings === []) {
            throw new InvalidArgumentException('Settings cannot be empty');
        }
        parent::__construct('app_settings', $options);
        parent::setOptions($options);
        $this->settings = $settings;
    }

    /**
     * @throws InvalidElementException
     */
    public function init(): void
    {
        foreach ($this->settings as $fieldsetName => $values) {
            $fieldset = new Fieldset($fieldsetName);
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
                        throw new InvalidElementException('Unsupported Element Type: ' . $type);
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
