<?php

declare(strict_types=1);

namespace App\Session;

use App\Session\Container;

trait SessionContainerAwareTrait
{
    /** @var Container $sessionContainer */
    protected $sessionContainer;

    public function setSessionContainer(Container $container)
    {
        $this->sessionContainer = $container;
    }

    public function getSessionContainer(): Container
    {
        return $this->sessionContainer;
    }
}
