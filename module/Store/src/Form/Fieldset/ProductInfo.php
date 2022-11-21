<?php

declare(strict_types=1);

namespace Store\Form\Fieldset;

use App\Form\Fieldset\FieldsetTrait;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Store\Model\Category;

class ProductInfo extends Fieldset implements InputFilterProviderInterface
{
    use FieldsetTrait;

    /** @var Category $category */
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
        $this->categoryValueOptions = $this->category->fetchSelectValueOptions();
        parent::__construct($name = null, []);
    }

    public function init()
    {
        $this->add([
            'name' => 'id',
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
        ]);
        $this->add([
            'name' => 'name',
            'type' => Text::class,
            'options' => [
                'label' => 'Product Name:',
            ],
        ]);
        $this->add([
            'name' => 'categoryId',
            'type' => Select::class,
            'options' => [
                'label' => 'Product Category:',
                'value_options' => $this->categoryValueOptions,
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => Textarea::class,
            'attributes' => [
                'id' => 'description',
            ],
            'options' => [
                'label' => 'Product Description:'
            ],
        ]);
        // cost decimal 10,2
        $this->add([
            'name' => 'cost',
            'type' => Number::class,
            'attributes' => [
                'step' => '.01',
            ],
            'options' => [
                'label' => 'Product Cost:'
            ],
        ]);
        // weight decimal
        $this->add([
            'name' => 'weight',
            'type' => Number::class,
            'attributes' => [
                'step' => '.01',
            ],
            'options' => [
                'label' => 'Product Weight:',
            ],
        ]);
        // manufacturer text
        $this->add([
            'name' => 'manufacturer',
            'type' => Text::class,
            'options' => [
                'label' => 'Product Manufaturer:',
            ],
        ]);
        // sku text
        $this->add([
            'name' => 'sku',
            'type' => Text::class,
            'options' => [
                'label' => 'Product SKU:'
            ],
        ]);
        // active bool checkbox
        $this->add([
            'name' => 'active',
            'type' => Checkbox::class,
            'options' => [
                'label' => 'Is product active?',
            ],
        ]);
        // onSale bool checkbox
        $this->add([
            'name' => 'onSale',
            'type' => Checkbox::class,
            'options' => [
                'label' => 'On Sale?',
            ],
        ]);
        // discount decimal 3,2
        $this->add([
            'name' => 'discount',
            'required' => false,
            'type' => Number::class,
            'attributes' => [
                'width' => '50%',
            ],
            'options' => [
                'label' => 'Discount Amount:'
            ],
        ]);
        // saleStartDate datepicker
        $this->add([
            'name' => 'saleStartDate',
            'type' => Date::class,
            'options' => [
                'label' => 'Sale Start Date:',
                'year_attributes' => [
                    'min_year' => -1,
                    'max_year' => 0,
                ],
                'month_attributes' => [],
                'day_attributes' => [],
            ],
        ]);
        // saleEndDate datepicker
        $this->add([
            'name' => 'saleEndDate',
            'type' => Date::class,
            'options' => [
                'label' => 'Sale End Date:',
                'year_attributes' => [
                    'min_year' => -1,
                    'max_year' => 0,
                ],
                'month_attributes' => [],
                'day_attributes' => [],
            ],
        ]);
    }
    public function getInputFilterSpecification()
    {
        return [];
    }
}
