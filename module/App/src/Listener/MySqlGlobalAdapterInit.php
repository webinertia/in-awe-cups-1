<?php

declare(strict_types=1);

namespace App\Listener;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;

class MySqlGlobalAdapterInit extends AbstractListenerAggregate
{
    protected AdapterInterface $adapter;
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'initGlobalAdapter'], 100);
    }

    public function initGlobalAdapter(MvcEvent $event)
    {
        GlobalAdapterFeature::setStaticAdapter($this->adapter);
    }
}
