<?php

declare(strict_types=1);

namespace User\Db;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;
use App\Model\ModelInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\ResultSet\ResultSet;
use User\Model\Guest;

final class UserGateway extends TableGateway
{
    use GatewayTrait;

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
