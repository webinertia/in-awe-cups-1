<?php

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
    protected $ownerIdColumn = 'ownerId';

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
        if (! empty($data['params']) && is_string($data['params'])) {
            $data['params'] = Decoder::decode($data['params'], Json::TYPE_ARRAY);
        }
        $this->storage = $data;
        return $storage;
    }
}
