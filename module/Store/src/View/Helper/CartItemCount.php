<?php

declare(strict_types=1);

namespace Store\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Store\Model\Cart;

class CartItemCount extends AbstractHelper
{
    /** @var Cart $cart */
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function __invoke()
    {
        return $this->cart->getItemCount();
    }
}
