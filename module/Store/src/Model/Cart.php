<?php

declare(strict_types=1);

namespace Store\Model;

use App\Model\ModelInterface;
use Laminas\Session\Container;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Store\Exception;
use Store\Model\OptionsPerProduct;
use Store\Model\Order;
use Store\Model\Product;

class Cart implements ModelInterface
{
    /** @var Container $container */
    protected $container;
    /**  @var Acl $acl */
    protected $acl;
    /** @var AuthenticationService $auth */
    protected $auth;
    /** @var UserService $user */
    protected $user;
    /** @var OptionsPerProduct $optionLookup */
    protected $optionLookup;
    /** @var Order $order */
    protected $order;
    /** @var Product $product */
    protected $product;
    /** @var int $itemCount */
    protected $itemCount;
    protected float $total;

    /**
     *
     * @param ContainerInterface $container
     * @return void
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        Container $container,
        OptionsPerProduct $optionLookup,
        Order $order,
        Product $product
    ) {
       // $this->acl = $container->get('Acl');
       // $this->auth = $container->get(AuthenticationService::class);
        $this->optionLookup = $optionLookup;
        $this->order        = $order;
        $this->product      = $product;
        $this->container    = $container;
        $this->container->setExpirationSeconds(604800);
    }

    public function addItem(array $item): void
    {
        if (! isset($item['id'])) {
            throw new Exception\DomainException('Item must contain an id.');
        }
        if ($this->container->offsetExists('items')) {
            $items = $this->container->offsetGet('items');
            // $items[][$item['id']] = $item;
            $items[] = $item;
            $this->container->offsetSet('items', $items);
        } else {
            $items = [];
            $items[] = $item;
            $this->container->offsetSet('items', $items);
        }
    }

    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function getItems()
    {
        return $this->container->offsetGet('items');
    }

    public function getResourceId(): ResourceInterface|string
    {
        return 'Cart';
    }

    public function emptyCart(): void
    {
        $this->container->offsetUnset('items');
    }

    public function getOwnerId(): mixed
    {
        return $this->session->userId;
    }
}
