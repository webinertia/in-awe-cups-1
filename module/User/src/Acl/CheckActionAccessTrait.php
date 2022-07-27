<?php

declare(strict_types=1);

namespace User\Acl;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

trait CheckActionAccessTrait
{
    public function isAllowed(
        ?ResourceInterface $resourceInterface = null,
        ?string $privilege = null
    ): bool {
        $isAllowed = false;
        if ($resourceInterface instanceof ResourceInterface) {
            $isAllowed = $this->acl()->isAllowed(
                $this->identity()->getIdentity(),
                $resourceInterface,
                $privilege ?? $this->params()->fromRoute('action')
            );
        } else {
            $isAllowed = $this->acl()->isAllowed(
                $this->identity()->getIdentity(),
                $this,
                $privilege ?? $this->params()->fromRoute('action')
            );
        }
        if (! $isAllowed) {
            $ident = $this->identity()->getIdentity();
            $this->warning(
                'User '
                . isset($ident->firstName) ? $ident->firstName . ' ' . $ident->username : $ident->userName
                . ' is not allowed to access '
                . $this->resourceId . ' with privilege: ' . $privilege
            );
            $this->response->setStatusCode(403);
        }
        return $isAllowed;
    }
}
