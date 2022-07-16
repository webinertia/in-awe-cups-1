<?php

declare(strict_types=1);

namespace ContentManager\Navigation\Page;

use Laminas\Json\Json;
use Laminas\Json\Decoder;
use Laminas\Navigation\Page\Mvc as MvcPage;
use Laminas\Permissions\Acl\ProprietaryInterface;

use function is_array;
use function is_string;

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
        if (isset($array['params']) && is_string($array['params'])) {
            $array['params'] = Decoder::decode($array['params'], Json::TYPE_ARRAY);
        }
        $this->setOptions($array);
    }

    public function getArrayCopy(): array
    {
        return $this->toArray();
    }

    public function getSqlSaveData(): array
    {
        $page = $this->toArray();
        if (is_array($page['params'])) {
            $page['params'] = Json::encode($page['params'], Json::TYPE_OBJECT);
        }
        return $page;
    }
}
