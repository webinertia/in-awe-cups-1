<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Exception\RuntimeException;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManager;

class TableGateway extends AbstractTableGateway
{
    /**
     * Name of the database table
     *
     * @var string $table
     */
    public $table;
    /**
     * @param string $table (database table name)
     * @param AbstractModel $arrayObjectPrototype
     * @param bool $enableEvents
     * @param null|string $listener
     * @return void
     * @throws RuntimeException
     */
    public function __construct(
        $table,
        ?EventManager $eventManager = null,
        ?ResultSet $resultSetPrototype = null,
        $enableEvents = false,
        ?AbstractListenerAggregate $listener = null
    ) {
        // Set the table name
        $this->table = $table;
        // Create a FeatureSet
        $this->featureSet = new FeatureSet();
        $this->featureSet->setTableGateway($this);
        // Add the desired features
        $this->featureSet->addFeature(new GlobalAdapterFeature());
        // if we have an instance of the events manager and events are enabled, add the event feature
        if ($enableEvents && $eventManager instanceof EventManager && $listener instanceof AbstractListenerAggregate) {
            $eventFeature = new EventFeature($eventManager);
            $eventManager = $eventFeature->getEventManager();
            $listener->attach($eventManager);
            $this->featureSet->addFeature($eventFeature);
        }
        $this->resultSetPrototype = $resultSetPrototype ?? new ResultSet();
        // inititalize this instance
        $this->initialize();
    }

    /**
     * @param ResultSet $resultSetPrototype
     */
    public function setResultSetPrototype($resultSetPrototype): void
    {
        $this->resultSetPrototype = $resultSetPrototype;
    }
}
