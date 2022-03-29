<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Form;
use RuntimeException;

class VerificationForm extends Form
{
    public function __construct($name, $options = null)
    {
        try {
            parent::__construct('VerificationForm');
            if (! empty($options)) {
                parent::setOptions($options);
            }
        } catch (RuntimeException $e) {
        }
    }
}
