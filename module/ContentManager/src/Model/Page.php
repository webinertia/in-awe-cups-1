<?php

/**
 * Intermediate Object that allows saving a Mvc Navigation page into a MySQL table
 */

declare(strict_types=1);

namespace ContentManager\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use ArrayObject;
use Laminas\Json\Decoder;
use Laminas\Json\Json;
use Laminas\Stdlib\Exception\InvalidArgumentException;

use function is_array;
use function is_object;
use function is_string;

final class Page extends AbstractGatewayModel
{
    /**
     * Exchange the array for another one.
     *
     * @param  array|ArrayObject|ArrayIterator|object $data
     */
    public function exchangeArray($data): array
    {
        if (! is_array($data) && ! is_object($data)) {
            throw new InvalidArgumentException(
                'Passed variable is not an array or object, using empty array instead'
            );
        }
        if (is_object($data) && ($data instanceof self || $data instanceof ArrayObject)) {
            $data = $data->getArrayCopy();
        }
        if (! is_array($data)) {
            $data = (array) $data;
        }
        $storage = $this->storage;
        if (isset($data['params']) && is_string($data['params'])) {
            $data['params'] = Decoder::decode($data['params'], Json::TYPE_ARRAY);
        }
        $this->storage = $data;
        return $storage;
    }

    public function getArrayCopy(): array
    {
        return [ // The are the only currently supported options
            'id'            => $this->offsetGet('id'),
            'parentId'      => $this->offsetGet('parentId'),
            'ownerId'       => $this->offsetGet('ownerId'),
            'label'         => $this->offsetGet('label'),
            'title'         => $this->offsetGet('title'),
            'class'         => $this->offsetGet('class'),
            'iconClass'     => $this->offsetGet('iconClass'),
            'order'         => $this->offsetGet('order'),
            'params'        => $this->offsetGet('params'),
            'resource'      => $this->offsetGet('resource'),
            'privilege'     => $this->offsetGet('privilege'),
            'visible'       => $this->offsetGet('visible'),
            'route'         => $this->offsetGet('route'),
            'uri'           => $this->offsetGet('uri'),
            'action'        => $this->offsetGet('action'),
            'query'         => $this->offsetGet('query'),
            'isGroupPage'   => $this->offsetGet('isGroupPage'),
            'allowComments' => $this->offsetGet('allowComments'),
            'content'       => $this->offsetGet('content'),
            'isLandingPage' => $this->offsetGet('isLandingPage'),
            'cmsType'       => $this->offsetGet('cmsType'),
            'createdDate'   => $this->offsetGet('createdDate'),
            'updatedDate'   => $this->offsetGet('updatedDate'),
        ];
    }

    protected function getSqlSaveUpdateArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * This will return ALL options of a Mvc Navigation Page once exchangeArray has been called
     */
    public function toArray(): array
    {
        return $this->storage;
    }
}
