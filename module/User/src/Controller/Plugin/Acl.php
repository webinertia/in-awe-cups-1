<?php

declare(strict_types=1);

namespace User\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Permissions\Acl\AclInterface;

final class Acl extends AbstractPlugin
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
    public function __invoke($resource = null, $privilege = null): bool
    {
        $controller = $this->getController();
        return $this->acl->isAllowed($controller->loadUser(), $resource, $privilege);
    }
}
