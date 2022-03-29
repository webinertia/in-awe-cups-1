<?php

declare(strict_types=1);

namespace User\Filter;

use Laminas\Filter\AbstractFilter;

use function is_string;
use function password_hash;

use const PASSWORD_DEFAULT;

class PasswordFilter extends AbstractFilter
{
    public function filter($value)
    {
        if (! is_string($value)) {
// eventually do some stuff here
        }

        return password_hash($value, PASSWORD_DEFAULT);
    }

    public function __invoke($value)
    {
        return $this->filter($value);
    }
}
