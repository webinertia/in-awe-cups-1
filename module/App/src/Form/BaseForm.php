<?Php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

class BaseForm extends Form implements InputFilterProviderInterface
{
    /** @inheritDoc */
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name);
        parent::setOptions($options);
    }

    public function addSubmit(): void
    {
        $this->add(
            [
                'name'       => 'submit',
                'type'       => Submit::class,
                'attributes' => [
                    'value' => 'Submit',
                    'id'    => 'submitbutton',
                ],
            ],
            ['priority' => -100]
        );
    }

    public function getInputFilterSpecification(): array
    {
        return [];
    }
}
