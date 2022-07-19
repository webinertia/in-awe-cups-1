<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use App\Controller\AbstractAppController;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Session\Container;

class RedirectPrev extends Redirect
{
    /**
     * @codingStandardsIgnoreStart
     * @property \Laminas\Session\Container $sessionsContainer
     * @var Container $sessionContainer
     * @codingStandardsIgnoreEnd
     * */
    protected $sessionContainer;

    public function __construct(Container $container)
    {
        $this->sessionContainer = $container;
    }

    public function __invoke()
    {
        /** @var AbstractAppController $controller */
        $controller = $this->getController();
        if ($this->sessionContainer->prevUrl !== null) {
            $controller->redirect()->toUrl($this->sessionContainer->prevUrl);
        } else {
            $controller->redirect()->toRoute('home');
        }
    }
}
