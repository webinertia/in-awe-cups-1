<?php

declare(strict_types=1);

namespace User\Db;

use App\Db\TableGateway\GatewayTrait;
use App\Db\TableGateway\TableGateway;
use App\Model\ModelInterface;
use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\Authentication\Result;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\ResultSet\ResultSet;
use Throwable;
use User\Model\Guest;

use function password_verify;

final class UserGateway extends TableGateway
{
    use GatewayTrait;

    /**
     * @param string $identity
     * @param string $credential
     */
    public function login($identity, $credential): Result
    {
        try {
            $callback    = function ($hash, $password) {
                return password_verify($password, $hash);
            };
            $authAdapter = new AuthAdapter(
                $this->getAdapter(),
                $this->getTable(),
                'userName',
                'password',
                $callback
            );
            $authAdapter->setIdentity($identity)
                        ->setCredential($credential);
            $select = $authAdapter->getDbSelect();
            $select->where('active = 1');
            // Perform the authentication query, saving the result
            $authService = new AuthService();
            $authService->setAdapter($authAdapter);
            $result = $authService->authenticate();
             // Handle the authentication query result
            switch ($result->getCode()) {
                case Result::SUCCESS:
                    /** do stuff for successful authentication **/
                    return $result;
               // break;
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    /** do stuff for nonexistent identity **/

                    return $result;
               // break;
                case Result::FAILURE_CREDENTIAL_INVALID:
                    /** do stuff for invalid credential **/

                    return $result;
               // break;
                default:
                    /** do stuff for other failure **/

                    return false;
               // break;
            }
        } catch (Throwable $th) {
            //$this->getLogger()->error($th->getMessage());
        }
    }

    public function fetchGuestContext(): ModelInterface
    {
        $guest     = (new Guest())->toArray();
        $resultSet = $this->getResultSetPrototype();
        if ($resultSet instanceof ResultSet) {
            $prototype = $resultSet->getArrayObjectPrototype();
            $prototype->exchangeArray($guest);
            return $prototype;
        }

        if ($resultSet instanceof HydratingResultSet) {
            $hydator   = $resultSet->getHydrator();
            $prototype = $resultSet->getObjectPrototype();
            $hydator->hydrate($guest, $prototype);
            return $prototype;
        }
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
            'sessionLength',
            'regDate',
            'active',
            'prefsTheme',
        ];
    }
}
