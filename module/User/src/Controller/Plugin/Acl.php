<?php

declare(strict_types=1);

namespace User\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Permissions\Acl\AclInterface;

final class Acl extends AbstractPlugin
{
    /** @var AclInterface $acl */
    protected $acl;

    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    public function __invoke(): self
    {
        return $this;
    }

    /** {@inheritDoc} */
    public function __call($name, $arguments): mixed
    {
        return $this->acl->{$name}(...$arguments);
    }
}
