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

    public function exchangeArray(array $array): void
    {
        $this->setOptions($array);
    }

    public function getArrayCopy(): array
    {
        return [
            'id'            => $this->id,
            'parentId'      => $this->parentId,
            'ownerId'       => $this->getOwnerId(),
            'label'         => $this->getLabel(),
            'title'         => $this->getTitle(),
            'class'         => $this->getClass(),
            'iconClass'     => $this->iconClass,
            'order'         => $this->getOrder(),
            'params'        => $this->getParams(),
            'rel'           => $this->getRel(),
            'rev'           => $this->getRev(),
            'resource'      => $this->getResource(),
            'privilege'     => $this->getPrivilege(),
            'visible'       => $this->isVisible(),
            'route'         => $this->getRoute(),
            'uri'           => $this->uri,
            'action'        => $this->getAction(),
            'query'         => $this->getQuery(),
            'isGroupPage'   => $this->isGroupPage,
            'allowComments' => $this->allowComments,
            'content'       => $this->content,
            'isLandingPage' => $this->isLandingPage,
            'createdDate'   => $this->createdDate,
            'updatedDate'   => $this->updatedDate,
        ];
    }
}
