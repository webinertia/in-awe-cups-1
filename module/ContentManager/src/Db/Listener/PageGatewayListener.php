<?php

declare(strict_types=1);

namespace ContentManager\Db\Listener;

use DateTime;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\DateTimeFormatter;
use Laminas\Json\Json;

final class PageGatewayListener extends AbstractListenerAggregate
{
    /** @var Json $json */
    protected $json;
    /** @var DateTimeFormatter $time */
    protected $time;
    /** @param string $format */
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
        $insert = $event->getParam('insert');
        if ($insert->order === null) {
            $gateway = $event->getTarget();
            $select  = (new Select())->from($gateway->getTable());
            $select->columns(['order'])->order('order DESC')->limit(1);
            $lastPage      = $gateway->selectWith($select)->current();
            $insert->order = $lastPage === null ? 1 : $lastPage->order + 1;
        }
        if ($insert->title !== 'homelandingpage' && (bool) $insert->showOnLandingPage) {
            $insert->parentId = 1;
        }
        $insert->values(
            [
                'class'         => 'nav-link',
                'createdDate'   => $this->time->filter(new DateTime()),
                'params'        => (string) Json::encode(['title' => $insert->title]),
                'route'         => 'page',
                'order'         => $insert->order,
                'visible'       => $insert->visible ?? 1,
                'parentId'      => $insert->parentId ?? 0,
                'resource'      => $insert->resource ?? 'page',
                'privilege'     => $insert->privilege ?? 'view',
                'isLandingPage' => $insert->isLandingPage ?? 0,
            ],
            Insert::VALUES_MERGE
        );
    }

    public function preUpdate(TableGatewayEvent $event): void
    {
        $update = $event->getParam('update');
        $set    = $update->getRawState('set');
        $data   = [
            'params'      => (string) Json::encode(['title' => $set['title']]),
            'route'       => 'page',
            'updatedDate' => $this->time->filter(new DateTime()),
            'parentId'    => $set['showOnLandingPage'] ? $set['parendId'] = 1 : $set['parentId'] = 0,
        ];
        $update->set($data, Update::VALUES_MERGE);
    }
}
