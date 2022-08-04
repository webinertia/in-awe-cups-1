<?php

/**
 * Trait for use within Controller classes
 */

declare(strict_types=1);

namespace User\Acl;

use App\Log\LogEvent;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

use function sprintf;

trait CheckActionAccessTrait
{
    public function isAllowed(
        ?ResourceInterface $resourceInterface = null,
        ?string $privilege = null
    ): bool {
        // set this as false
        $isAllowed = false;
        // if we pass a resourceInterface instance we need to use it
        if ($resourceInterface instanceof ResourceInterface) {
            $isAllowed = $this->acl->isAllowed(
                $this->identity()->getIdentity(),
                $resourceInterface,
                $privilege ?? $this->params()->fromRoute('action')
            );
        }
        // we did not pass a resourceInterface instance but this instance is a resourceInterface then check it
        if ($resourceInterface === null && $this instanceof ResourceInterface) {
            $isAllowed = $this->acl->isAllowed(
                $this->identity()->getIdentity(),
                $this,
                $privilege ?? $this->params()->fromRoute('action')
            );
        }
        // if we are not allowed to access the action we need to log it
        if (! $isAllowed) {
            $ident = $this->identity()->getIdentity();
            $this->getEventManager()->trigger(
                LogEvent::NOTICE,
                sprintf(
                    $this->getTranslator()->translate(
                        'log_forbidden_known_action_403'
                    ),
                    $this->params()->fromRoute('action')
                )
            );
            $this->response->setStatusCode(403);
        }
        return $isAllowed;
    }
}
