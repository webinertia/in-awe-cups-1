<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Model\AbstractModel;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\AbstractTableGateway;
use RuntimeException;

use function sprintf;

trait ModelTrait
{
    /** @var AbstractTableGateway $db */
    protected $db;
/** @var ResultSet $resultSet */
    protected $resultSet;
/**
 * @param mixed $column
 * @param mixed $value
 * @return mixed
 * @throws RuntimeException
 */
    public function fetchByColumn($column, $value)
    {
        $column    = (string) $column;
        $resultSet = $this->db->select([$column => $value]);
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(sprintf('Could not fetch column: ' . $column . ' with value: ' . $value));
        }
        return $row;
    }

    public function fetchColumns($column, $value, ?array $columns)
    {
        $resultSet = $this->db->select(function (Select $select) use ($column, $value, $columns) {
            $select->columns($columns)->where([$column => $value]);
        });
        $row       = $resultSet->current();
        if (! $row) {
            throw new RuntimeException(sprintf('Could not fetch row with column: ' . $column . ' with value: ' . $value));
        }
        return $row;
    }

    public function select()
    {
        return $this->db->select();
    }

    public function fetchall()
    {
        return $this->db->select();
    }

    public function insert(AbstractModel $model)
    {
        $this->db->insert($model->toArray());
        return $this->db->getLastInsertValue();
    }

    public function update(AbstractModel $model, $where = null, ?array $joins = null)
    {
        return $this->db->update($model->toArray(), $where, $joins);
    }

    public function getTable()
    {
        return $this->db->getTable();
    }

    public function getAdapter()
    {
        return $this->db->getAdapter();
    }
}
