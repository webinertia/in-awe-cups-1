<?php

declare(strict_types=1);

namespace ContentManager\Db\Listener;

use DateTime;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\DateTimeFormatter;
use Laminas\Json\Json;

final class InsertUpdateListener extends AbstractListenerAggregate
{
    /** @var Json $json */
    protected $json;
    /** @var DateTimeFormatter $time */
    protected $time;
    public function __construct($format = 'm-j-Y g:i:s')
    {
        $this->time = new DateTimeFormatter(['format' => $format]);
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(EventFeature::EVENT_PRE_INSERT, [$this, 'preInsert'], $priority);
        $this->listeners[] = $events->attach(EventFeature::EVENT_PRE_UPDATE, [$this, 'preUpdate'], $priority);
    }

    public function preInsert(TableGatewayEvent $event): void
    {
        /** @var Insert $insert */
        $insert = $event->getParam('insert');
        $insert->values(['createdDate' => $this->time->filter(new DateTime())], Insert::VALUES_MERGE);
    }

    public function preUpdate(TableGatewayEvent $event): void
    {
        $update = $event->getParam('update');
        $set    = $update->getRawState('set');
        $data   = [
            'params'      => Json::encode(['title' => $set['title']]),
            'updatedDate' => $this->time->filter(new DateTime()),
        ];
        $update->set($data, Update::VALUES_MERGE);
    }
}
