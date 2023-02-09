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
    protected $session;

    public function __construct(Cart $cart, array $config)
    {
        parent::__construct($config);
        $this->cart = $cart;
    }

    public function indexAction(): ModelInterface
    {
        $this->view->setVariables(
            [
                'products' => $this->cart->getItems()
            ]
        );
        return $this->view;
    }

    public function addItemAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }
        if ($this->request->isPost()) {
            $this->cart->addItem($this->request->getPost()->toArray());
        }
        $items = $this->cart->getItems();
        return $this->view;
    }

    public function removeItemAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }
        return $this->view;
    }

    public function checkoutAction(): ModelInterface
    {
        return $this->view;
    }

    public function emptyCartAction(): ModelInterface
    {
        $this->cart->emptyCart();
        return $this->view;
    }

    public function applyCouponAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }
        return $this->view;
    }
}
