<?php

declare(strict_types=1);

namespace User\Acl;

trait CheckActionAccessTrait
{
    public function isAllowed(): bool
    {
        return $this->acl()->isAllowed($this->identity()->getIdentity(), $this, $this->params()->fromRoute('action'));
    }
}
