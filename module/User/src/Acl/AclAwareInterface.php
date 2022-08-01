<?php

declare(strict_types=1);

namespace User\Acl;

use Laminas\Permissions\Acl\AclInterface;

interface AclAwareInterface
{
    public function setAcl(AclInterface $acl);

    public function getAcl(): AclInterface;
}
