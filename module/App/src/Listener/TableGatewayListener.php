<?php

declare(strict_types=1);

namespace App\Listener;

use Laminas\Db\Sql\Insert;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

final class TableGatewayListener extends AbstractListenerAggregate
{
    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            EventFeature::EVENT_PRE_INSERT,
            [
                $this,
                'preInsert',
            ],
            $priority
        );
    }

    public function preInsert(TableGatewayEvent $event): void
    {
        /** @var Insert $insert */
        $insert = $event->getParam('insert');

        // Modify values of the insert
        //$insert->values(['age' => 24], Insert::VALUES_MERGE);
    }
}
