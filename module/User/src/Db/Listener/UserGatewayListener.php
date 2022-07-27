<?php

declare(strict_types=1);

namespace User\Db\Listener;

use App\Service\Email;
use DateTime;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\DateTimeFormatter;

final class UserGatewayListener extends AbstractListenerAggregate
{
    /** @var Email $mailService */
    protected $mailService;
    /** @inheritDoc */
    public function __construct(Email $mailService, $format = 'm-j-Y g:i:s')
    {
        $this->mailService = $mailService;
        $this->time        = new DateTimeFormatter(['format' => $format]);
    }

    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(EventFeature::EVENT_PRE_INSERT, [$this, 'preInsert'], $priority);
        $this->listeners[] = $events->attach(EventFeature::EVENT_PRE_UPDATE, [$this, 'preUpdate'], $priority);
    }

    public function preInsert(TableGatewayEvent $event)
    {
    }

    public function postInsert(TableGatewayEvent $event)
    {
    }

    public function preUpdate(TableGatewayEvent $event)
    {
    }
}
