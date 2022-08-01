<?php

declare(strict_types=1);

namespace App\Model;

use Closure;
use Laminas\Db\Exception\RuntimeException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\Exception\InvalidArgumentException;
use Laminas\Db\TableGateway\Exception\RuntimeException as TableGatewayRuntimeException;

use function sprintf;

trait ModelTrait
{
    /** @var TableGatewayInterface $gateway */
    protected $gateway;
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
        $resultSet = $this->gateway->select([$column => $value]);
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(sprintf('Could not fetch column: ' . $column . ' with value: ' . $value));
        }
        return $row;
    }

    /**
     * @param string $column
     * @param int|string $value
     * @param null|array $columns
     * @throws TableGatewayRuntimeException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchColumns($column, $value, ?array $columns = ['*']): ModelInterface
    {
        $resultSet = $this->gateway->select(function (Select $select) use ($column, $value, $columns) {
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

    public function fetchAll(): ResultSetInterface
    {
        return $this->gateway->select();
    }

    /**
     * @param Where|Closure|string|array $where
     */
    public function delete($where): int
    {
        return $this->gateway->delete($where);
    }
}
