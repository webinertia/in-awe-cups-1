<?php

declare(strict_types=1);

namespace ContentManager\Navigation\Page;

use Laminas\Navigation\Page\Mvc as MvcPage;
use Laminas\Permissions\Acl\ProprietaryInterface;

final class Mvc extends MvcPage implements ProprietaryInterface
{
    /** @var string|int $ownerId */
    protected $ownerId;
    public function getOwnerId(): mixed
    {
        return $this->ownerId;
    }

    /** @param string|int $ownerId */
    public function setOwnerId($ownerId): void
    {
        $this->ownerId = $ownerId;
    }
}
