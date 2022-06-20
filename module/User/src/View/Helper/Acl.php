<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;

final class Acl extends AbstractHelper
{
    /** @var AclInterface $acl */
    /** @return void */
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
