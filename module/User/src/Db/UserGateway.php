<?php

declare(strict_types=1);

namespace User\Db;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;
use App\Model\ModelInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManager;
use User\Model\Guest;

final class UserGateway extends TableGateway
{
    use GatewayTrait;

    public function __construct(
        $table,
        ?EventManager $eventManager = null,
        ?ResultSetInterface $resultSetInterface = null,
        $enableEvents = false,
        ?AbstractListenerAggregate $listener = null,
        ?AdapterInterface $adapter = null,
    ) {
        // Set the table name
        $this->table = $table;
        $this->adapter = $adapter;
        // Create a FeatureSet
        $this->featureSet = new FeatureSet();
        $this->featureSet->setTableGateway($this);
        // Add the desired features
        //$this->featureSet->addFeature(new GlobalAdapterFeature());
        // if we have an instance of the events manager and events are enabled, add the event feature
        if ($enableEvents && $eventManager instanceof EventManager && $listener instanceof AbstractListenerAggregate) {
            $eventFeature = new EventFeature($eventManager);
            $eventManager = $eventFeature->getEventManager();
            $listener->attach($eventManager);
            $this->featureSet->addFeature($eventFeature);
        }
        $this->resultSetPrototype = $resultSetInterface ?? new ResultSet();
        // inititalize this instance
        $this->initialize();
    }

    public function fetchGuestContext(): ModelInterface
    {
        $prototype = false;
        $guest     = (new Guest())->toArray();
        $resultSet = $this->getResultSetPrototype();
        if ($resultSet instanceof ResultSet) {
            $prototype = $resultSet->getArrayObjectPrototype();
            $prototype->exchangeArray($guest);
        }
        if ($resultSet instanceof HydratingResultSet) {
            $hydator   = $resultSet->getHydrator();
            $prototype = $resultSet->getObjectPrototype();
            $hydator->hydrate($guest, $prototype);
        }
        return $prototype;
    }

    public function getContextColumns(): array
    {
        return [
            'id',
            'userName',
            'email',
            'role',
            'firstName',
            'lastName',
            'profileImage',
            'age',
            'birthday',
            'gender',
            'race',
            'bio',
            'companyName',
            'jobTitle',
            'mobileNumber',
            'officeNumber',
            'homeNumber',
            'street',
            'aptNumber',
            'city',
            'state',
            'zip',
            'country',
            'webUrl',
            'github',
            'twitter',
            'instagram',
            'facebook',
            'linkedin',
            'slack',
            'sessionLength',
            'regDate',
            'active',
            'prefsTheme',
        ];
    }
}
