<?php

declare(strict_types=1);

namespace Store\Api\Form;

use App\Form\BaseForm;

use App\Form\Fieldset\FieldsetTrait;
use DateTimeInterface;
use Dojo\Form\Element\CurrencyTextBox;
use Dojo\Form\Element\DateTextBox;
use Dojo\Form\Element\Editor;
use Dojo\Form\Element\TextBox;
use Dojo\Form\Element\ValidationTextBox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\StringLength;
use Store\Model\Category;

class ApiProductForm extends BaseForm
{
    public function __construct($name = 'apiProductForm', $options = [])
    {
        $options['server']['time_format'] = DateTimeInterface::RFC3339;
        parent::__construct($name, $options);
    }

    public function init(): void
    {
        $this->add([
            'name' => 'id',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'categoryId',
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'userId',
            'type' => Hidden::class,
        ]);
        // createdDate autogen timestamp
        $this->add([
            'name' => 'createdDate',
            'type' => Hidden::class,
            'attributes' => [
                'value' => date($this->getOptions()['server']['time_format']),
            ],
        ]);
        $this->add([
            'name' => 'label',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'placeholder'     => 'Product Name:',
                'data-dojo-props' => 'validator:dojox.validate.isText, constraints:{minLength:1, maxLength:255}, invalidMessage:\'Must be between 1 and 255 characters.\'',
            ],
        ]);
        // cost decimal 10,2
        $this->add([
            'id'   => 'cost',
            'name' => 'cost',
            'type' => CurrencyTextBox::class,
            'attributes' => [
                'required'    => true,
                'Placeholder' => 'Product Cost: (10.00)',
            ],
        ]);
        // weight decimal
        $this->add([
            'name' => 'weight',
            'type' => ValidationTextBox::class,
            'attributes' => [
                'required'    => true,
                'placeholder' => 'Shipping weight: ex 10.5',
            ],
        ]);
        // manufacturer text
        $this->add([
            'name' => 'manufacturer',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product Manufacturer',
                'value'       => 'In Awe Cups & More',
            ],
        ]);
        // sku text
        $this->add([
            'name' => 'sku',
            'type' => TextBox::class,
            'attributes' => [
                'placeholder' => 'Product SKU:'
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => Editor::class,
            'attributes' => [
                'data-dojo-props' => 'extraPlugins:
                [
                    \'foreColor\',
                    \'hiliteColor\'
                ]',
            ],
        ]);
        $this->addSubmit();
    }
}
