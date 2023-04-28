<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use Store\Model\Cart;

final class CartController extends AbstractAppController
{
    /** @var Cart $cart */
    protected $cart;

    public function __construct(Cart $cart, array $config)
    {
        parent::__construct($config);
        $this->cart = $cart;
    }

    public function indexAction(): ModelInterface
    {
        return $this->view;
    }

    public function addItemAction(): ModelInterface
    {
        return $this->view;
    }
}
