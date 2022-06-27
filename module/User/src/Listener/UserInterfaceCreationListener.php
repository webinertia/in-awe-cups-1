<?php

declare(strict_types=1);

namespace User\Listener;

use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use User\Model\Roles;

final class UserInterfaceCreationListener extends AbstractListenerAggregate
{
    /** @var Roles $roles */
    public function __construct()
    {
        $this->roles = new Roles();
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(EventFeature::EVENT_POST_SELECT, [$this, 'postSelect'], $priority);
    }

    public function postSelect(TableGatewayEvent $event): void
    {
        // $resultSet = $event->getParam('result_set');
    }
}
