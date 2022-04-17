<?php

declare(strict_types=1);

namespace ContentManager\Model;

use ArrayObject;
use Laminas\Json\Decoder;
use Laminas\Json\Encoder;
use Laminas\Json\Json;
use Laminas\Navigation\Page\Mvc;
use Laminas\Navigation\Page\Uri;
use Laminas\Router\RouteStackInterface;
use Laminas\Stdlib\Exception\InvalidArgumentException;
use Webinertia\ModelManager\AbstractModel;
use Webinertia\ModelManager\ModelTrait;

use function is_array;
use function is_object;
use function is_string;

class Pages extends AbstractModel
{
    use ModelTrait;

    /** @var RouteStackInterface $router */
    protected $router;
    public function setRouter(RouteStackInterface $router)
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
     * @return array
     */
    public function exchangeArray($data)
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
            unset($row->content);
            if (! empty($row->uri)) {
                $page = Uri::factory($row->getArrayCopy());
            }
            if (! empty($row->route)) {
                $page = Mvc::factory($row->getArrayCopy());
            }
            $page->setRouter($this->router);
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     * @return mixed
     * @throws TableGatewayRuntimeException
     * @throws InvalidArgumentException
     */
    public function save()
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

    // public function getArrayCopy(): array
    // {
    //     $params = $this->offsetGet('params');
    //     if ($params !== null) {
    //         $this->offsetSet('params', unserialize($params));
    //     }
    //     return $this->storage;
    // }
}
