<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use App\Model\ModelInterface;
use Laminas\Db\Exception\RuntimeException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;

use function sprintf;

trait GatewayTrait
{
    /** @var AbstractTableGateway $db */
    protected $db;
    /** @var ResultSet $resultSet */
    protected $resultSet;
    /**
     * @param string $column
     * @param mixed $value
     * @throws RuntimeException
     */
    public function fetchByColumn($column, $value): ModelInterface
    {
        $column    = (string) $column;
        $resultSet = $this->select([$column => $value]);
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(sprintf('Could not fetch column: ' . $column . ' with value: ' . $value));
        }
        return $row;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param null|array $columns
     * @throws TableGatewayRuntimeException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchColumns($column, $value, ?array $columns = ['*']): ModelInterface
    {
        $resultSet = $this->select(function (Select $select) use ($column, $value, $columns) {
            $select->columns($columns)->where([$column => $value]);
        });
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(
                sprintf('Could not fetch row with column: ' . $column . ' with value: ' . $value)
            );
        }
        return $row;
    }

    public function fetchAll(): object
    {
        return $this->select();
    }
}
