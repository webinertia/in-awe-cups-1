<?php

declare(strict_types=1);

namespace User\Model;

use Application\Model\AbstractModel;
use Application\Model\ModelTrait;
use Laminas\Authentication\AuthenticationService as AuthService;
use Laminas\Authentication\Result;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\Exception\RuntimeException;
use Laminas\Log\Logger;
use Throwable;
use User\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;

use function password_verify;

class Users extends AbstractModel
{
    use ModelTrait;

    /** @var string $column */
    protected $column;
    /**
     * @param Users $user
     * @return Result|bool
     * @throws InvalidArgumentException
     */
    public function login(self $user): Result
    {
        try {
            $callback    = function ($hash, $password) {
                return password_verify($password, $hash);
            };
            $authAdapter = new AuthAdapter($user, $this->db->getAdapter(), $this->config->db->users_table_name, $this->config->db->auth_identity_column, $this->config->db->auth_credential_column, $callback);
            $authAdapter->setIdentity($user->userName)
                        ->setCredential($user->password);
            $select = $authAdapter->getDbSelect();
            $select->where('active = 1')
                   ->where('verified = 1');
            // Perform the authentication query, saving the result
            $authService = new AuthService();
            $authService->setAdapter($authAdapter);
            $result = $authService->authenticate();
             // Handle the authentication query result
            switch ($result->getCode()) {
                case Result::SUCCESS:
                    /** do stuff for successful authentication **/
                    //$omitColumns = ['password'];
                    //$user = $authAdapter->getResultRowObject(null, $omitColumns);
                    return $result;
                break;
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    /** do stuff for nonexistent identity **/

                    return $result;
                break;
                case Result::FAILURE_CREDENTIAL_INVALID:
                    /** do stuff for invalid credential **/

                    return $result;
                break;
                default:
                    /** do stuff for other failure **/

                    return false;
                break;
            }
        } catch (Throwable $th) {
            $this->logger->log(Logger::ERR, $th->getMessage());
        }
    }

    /**
     * @param string $userName
     * @return self
     * @throws InvalidArgumentException
     * @throws ExceptionRuntimeException
     */
    public function fetchUserContext($userName): object
    {
        $userName = (string) $userName;
        // $row = $this->fetchColumns('userName', $userName, $this->userContext);

        $select = new Select();
        $select
            ->from($this->config->db->users_table_name)
            ->where([$this->config->db->users_table_name . '.userName' => $userName])
            ->join($this->config->db->user_roles_table_name, $this->config->db->users_table_name . '.role =' . $this->config->db->user_roles_table_name . '.role')
            ->columns([
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
                'verified',
            ])
            ->order([
                $this->config->db->user_roles_table_name . '.label ASC',
                $this->config->db->users_table_name . '.regDate DSC',
            ]);
        return $this->db->selectWith($select)->current();
    }

    /**
     * @return ResultSet
     * @throws InvalidArgumentException
     * @throws ExceptionRuntimeException
     */
    public function fetchAllUsers(): object
    {
        $select = new Select();
        $select
            ->from($this->config->db->user_roles_table_name)
            ->join($this->config->db->users_table_name, $this->config->db->users_table_name . '.role =' . $this->config->db->user_roles_table_name . '.role')
            ->columns($this->userContext)
            ->order([
                $this->config->db->user_roles_table_name . '.label ASC',
                $this->config->db->users_table_name . '.regDate DSC',
            ]);
        return $this->db->selectWith($select);
    }

    /** @return array */
    public function getLogData(): array
    {
        return [
            'userId'    => $this->offsetGet('id'),
            'userName'  => $this->offsetGet('userName'),
            'firstName' => $this->offsetExists('firstName') ? $this->offsetGet('firstName') : null,
            'lastName'  => $this->offsetExists('lastName') ? $this->offsetGet('lastName') : null,
        ];
    }

    /**
     * @param mixed $where
     * @param null|array $joins
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function update(AbstractModel $model, $where = null, ?array $joins = null): int
    {
        return $this->db->update($model->getArrayCopy(), $where, $joins);
    }

    public function getOwnerId(): int
    {
        if (! $this->offsetExists('userId') && $this->offsetExists('userName')) {
            return $this->offsetGet('id');
        }
    }

    public function fetchGuestContext(): array
    {
        return [
            'id'       => null,
            'userName' => 'Guest',
            'role'     => 'guest',
        ];
    }
}
