<?php

declare(strict_types=1);

namespace ContentManager\Model;

use ArrayObject;
use Laminas\Json\Decoder;
use Laminas\Json\Encoder;
use Laminas\Json\Json;
use Laminas\Router\RouteStackInterface;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Webinertia\ModelManager\AbstractModel;
use Webinertia\ModelManager\ModelTrait;

use function is_array;
use function is_object;
use function is_string;

final class Pages extends AbstractModel
{
    use ModelTrait;

    /** @var RouteStackInterface $router */
    protected $router;
    public function setRouter(RouteStackInterface $router): void
    {
        $this->router = $router;
    }

    public function getRouter(): RouteStackInterface
    {
        return $this->router;
    }

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

    public function fetchMenu(): array
    {
        $result = $this->db->select();
        $pages  = [];
        foreach ($result as $row) {
            $pages[] = $row->getArrayCopy();
        }
        return $pages;
    }

    /**
     * @throws TableGatewayRuntimeException
     * @throws InvalidArgumentException
     */
    public function save(): mixed
    {
        if ($this->offsetExists('params') && is_array($this->offsetGet('params'))) {
            $this->offsetSet('params', Encoder::encode($this->offsetGet('params')));
        }
        if (! $this->offsetExists('id')) {
            return $this->insert($this);
        } else {
            return $this->update($this);
        }
    }
}
