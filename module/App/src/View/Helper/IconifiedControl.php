<?php

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Permissions\Acl\Acl;
use Laminas\View\Helper\AbstractHelper;

class IconifiedControl extends AbstractHelper
{
    /** @var Acl */
    protected $acl;

    /**
     * @param Debug $debug
     */
    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    public function __invoke(): self
    {
        return $this;
    }
}
