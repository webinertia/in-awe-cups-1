<?php

/**
 * Usage: All implementing classes must define the $resourceId property. If not defined, the class name will be used.
 */

declare(strict_types=1);

namespace User\Acl;

trait ResourceAwareTrait
{
    public function getResourceId(): string
    {
        if ($this->resourceId !== null) {
            return $this->resourceId;
        }
        return static::class;
    }
}
