<?php

declare(strict_types=1);

namespace user\Authentication\Adapter\DbTable;

use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter as CallbackCheck;
use Laminas\Authentication\Result as AuthenticationResult;
use Laminas\Db\Adapter\Adapter;
use User\Model\Users as User;

use function array_keys;
use function in_array;

class CallbackCheckAdapter extends CallbackCheck
{
    public function __construct(User $usrModel, Adapter $dbAdapter, $tableName = null, $identityColumn = null, $credentialColumn = null, $credentialValidationCallback = null)
    {
        $this->usrModel = $usrModel;
        parent::__construct($dbAdapter, $tableName, $identityColumn, $credentialColumn, $credentialValidationCallback);
    }

        /**
         * _authenticateValidateResult() - This method attempts to validate that
         * the record in the resultset is indeed a record that matched the
         * identity provided to this adapter.
         *
         * @param  array $resultIdentity
         * @return AuthenticationResult
         */

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return User
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null): object
    {
        if (! $this->resultRow) {
            return false;
        }

        //$returnObject = new stdClass();

        if (null !== $returnColumns) {
            $availableColumns = array_keys($this->resultRow);
            foreach ((array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $this->usrModel->{$returnColumn} = $this->resultRow[$returnColumn];
                }
            }
            return $this->usrModel;
        } elseif (null !== $omitColumns) {
            $omitColumns = (array) $omitColumns;
            foreach ($this->resultRow as $resultColumn => $resultValue) {
                if (! in_array($resultColumn, $omitColumns)) {
                    $this->usrModel->{$resultColumn} = $resultValue;
                }
            }
            return $this->usrModel;
        }

        foreach ($this->resultRow as $resultColumn => $resultValue) {
            $this->usrModel->{$resultColumn} = $resultValue;
        }
        return $this->usrModel;
    }
}
