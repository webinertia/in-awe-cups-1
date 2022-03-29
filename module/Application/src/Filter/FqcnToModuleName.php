<?php

declare(strict_types=1);

namespace Application\Filter;

use Laminas\Filter\FilterInterface;

use function strpos;
use function substr;

class FqcnToModuleName implements FilterInterface
{
    public function filter($value): string
    {
        $value = (string) $value;
        return substr($value, 0, strpos($value, '\\'));
    }
}
