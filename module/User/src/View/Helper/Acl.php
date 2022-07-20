<?php

/**
 * @method \Laminas\Permissions\Acl\Acl isAllowed($role = null, $resource = null, $privilege = null)
 */

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\AbstractHelper;

final class Acl extends AbstractHelper
{
    /** @var AclInterface $acl */
    protected $acl;
    /** @return void */
    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * Primary call to isAllowed($role, $resource, $privilege)
     *
     * @param mixed $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->acl->{$name}(...$arguments);
    }
}
