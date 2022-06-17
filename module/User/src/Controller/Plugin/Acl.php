<?php

declare(strict_types=1);

namespace User\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Permissions\Acl\AclInterface;

class Acl extends AbstractPlugin
{
    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param mixed $role
     * @param mixed $resource
     * @param mixed $privilege
     */
    public function __invoke($role = null, $resource = null, $privilege = null): bool
    {
        return $this->acl->isAllowed($role, $resource, $privilege);
    }
}
