<?php

declare(strict_types=1);

namespace Store\Db\TableGateway\Listener;

use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

final class ProductsListener extends AbstractListenerAggregate
{
    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(EventFeature::EVENT_POST_INSERT, [$this, 'postInsert'], $priority);
    }

    public function postInsert(TableGatewayEvent $event): void
    {

    }
}
