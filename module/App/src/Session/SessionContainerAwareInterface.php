<?php

declare(strict_types=1);

namespace App\Session;

use App\Session\Container;

interface SessionContainerAwareInterface
{
    public function setSessionContainer(Container $container);

    public function getSessionContainer(): Container;
}
