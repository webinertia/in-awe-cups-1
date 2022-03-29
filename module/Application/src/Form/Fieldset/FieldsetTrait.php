<?php

declare(strict_types=1);

namespace Application\Form\Fieldset;

use function in_array;

trait FieldsetTrait
{
    /**
     * @param array|null $exclude
     * @return array
     */
    public function getElementNames(?array $exclude = null)
    {
        $elements = $this->getElements();
        $names    = [];
        foreach ($elements as $key => $object) {
            if (! empty($exclude) && ! in_array($key, $exclude)) {
                $names[] = $key;
            }
        }
        return $names;
    }
}
