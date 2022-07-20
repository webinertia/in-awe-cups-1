<?php

declare(strict_types=1);

namespace User\Acl;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

trait CheckActionAccessTrait
{
    public function isAllowed(?ResourceInterface $resourceInterface = null): bool
    {
        $isAllowed = false;
        if ($resourceInterface instanceof ResourceInterface) {
            $isAllowed = $this->acl()->isAllowed(
                $this->identity()->getIdentity(),
                $resourceInterface,
                $this->params()->fromRoute('action')
            );
        } else {
            $isAllowed = $this->acl()->isAllowed(
                $this->identity()->getIdentity(),
                $this,
                $this->params()->fromRoute('action')
            );
        }
        return $isAllowed;
    }
}
