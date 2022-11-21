<?php

namespace Store\Form;

use App\Form\BaseForm;
use Laminas\Form\Element\Hidden;
use Store\Form\Fieldset\ProductInfo;

class ProductForm extends BaseForm
{
    public function init()
    {
        $this->add([
            // you are not required to set a name here as the fieldset overrides it during construction
            'type' => Hidden::class,
        ]);
        $this->add([
            'name' => 'product-info',
            'type' => ProductInfo::class,
        ]);
    }
}
