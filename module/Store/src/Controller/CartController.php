<?php

declare(strict_types=1);

namespace Store\Controller;

use App\Controller\AbstractAppController;
use Laminas\Navigation\Navigation;
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
        $navigation = $this->getService(Navigation::class);
        // set shop navigation tab as active
        $page = $navigation->findOneBy('label', 'Shop');
        $page->active = true;
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
            //$this->view = new JsonModel();
        }
        if ($this->request->isPost()) {
            $item = $this->request->getPost()->toArray();
            $item['quantity'] = (int) $item['quantity'];
            $this->cart->addToCart($item);
        }
        $items = $this->cart->getItems();
        return $this->view;
    }

    public function removeItemAction(): ModelInterface
    {
        $this->ajaxAction();
        $params = $this->request->getQuery()->toArray();
        if ($this->cart->removeItem($params['id'], $params['cartId'])) {
            // set proper status code
        } else {
            $this->response->setStatusCode(500);
        }
        return $this->view;
    }

    public function checkoutAction(): ModelInterface
    {
        return $this->view;
    }

    public function emptyCartAction(): ModelInterface
    {
        $this->ajaxAction();
        $this->cart->emptyCart();
        $this->view->setVariables(
            [
                'products' => $this->cart->getItems()
            ]
        );
        return $this->view;
    }

    public function applyCouponAction(): ModelInterface
    {
        if ($this->ajaxAction()) {
            $this->view = new JsonModel();
        }
        return $this->view;
    }

    public function getBadgeCountAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getSubtotalAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getShippingAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }

    public function getTotalAction(): ModelInterface
    {
        $this->ajaxAction();
        return $this->view;
    }
}
