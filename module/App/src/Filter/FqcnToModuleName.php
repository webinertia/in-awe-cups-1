<?php

declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\FilterInterface;

use function strpos;
use function substr;

final class FqcnToModuleName implements FilterInterface
{
    /** @param mixed $value */
    public function filter($value): string
    {
        $value = (string) $value;
        return substr($value, 0, strpos($value, '\\'));
    }
}
