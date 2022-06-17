<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Session\Container;

use function method_exists;

class RedirectPrev extends Redirect
{
    public function __construct(Container $container)
    {
        $this->sessionContainer = $container;
    }

    public function __invoke()
    {
        $controller = $this->getController();
        if (! $controller || ! method_exists($controller, 'plugin')) {
            throw new DomainException(
                'Redirect plugin requires a controller that defines the plugin() method'
            );
        }
        $request = $controller->getRequest();
        $server  = $request->getServer();
        if ($this->sessionContainer->prevUrl !== null) {
            $this->toUrl($this->sessionContainer->prevUrl);
        } else {
            $this->toRoute('home');
        }
    }
}
