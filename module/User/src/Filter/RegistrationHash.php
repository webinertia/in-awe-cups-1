<?php

declare(strict_types=1);

namespace User\Filter;

use Laminas\Filter\Exception\InvalidArgumentException;
use Laminas\Filter\FilterInterface;

use function gettype;
use function is_array;
use function password_hash;

use const PASSWORD_DEFAULT;

final class RegistrationHash implements FilterInterface
{
    /**
     * @param array $value ['email' => $email, 'timestamp' => $timestamp]
     */
    public function filter($value): string
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException('Expected $value to be of type array, received: ' . gettype($value));
        }
        if (isset($value['email']) && $value['timestamp']) {
            return password_hash($value['email'] . $value['timestamp'], PASSWORD_DEFAULT);
        }
    }
}
