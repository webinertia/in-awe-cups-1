<?php

declare(strict_types=1);

namespace User\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Exception\InvalidArgumentException;

use function gettype;
use function is_string;
use function password_hash;

use const PASSWORD_DEFAULT;

final class PasswordFilter extends AbstractFilter
{
    /** @param mixed $value */
    public function filter($value): string
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('Expected $value to be a string, received: ' . gettype($value));
        }

        return password_hash($value, PASSWORD_DEFAULT);
    }

    /** @param string $value */
    public function __invoke($value): string
    {
        return $this->filter($value);
    }
}
