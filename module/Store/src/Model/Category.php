<?php

declare(strict_types=1);

namespace Store\Model;

use App\Db\TableGateway\AbstractGatewayModel;
use App\Model\ModelInterface;
use App\Model\ModelTrait;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Where;
use Store\Db\TableGateway\CategoriesTable;

use function count;
use function explode;
use function is_array;

final class Category extends AbstractGatewayModel implements ModelInterface
{
    use ModelTrait;

    /** @var ResultSet|array $children */
    protected $children;

    public function __construct(?CategoriesTable $categoriesTable = null)
    {
        parent::__construct([]);
        if ($categoriesTable !== null) {
            $this->gateway = $categoriesTable;
        }
    }

    public function fetchSelectValueOptions()
    {
        return $this->gateway->fetchSelectValueOptions();
    }

    /**
     * $model replaces $set as the set is derived from model data
     * @param string|array|Closure $where
     * @param  null|array $joins
     * @return int
     */
    public function save($model, $where = null, ?array $joins = null): int
    {
        $set = $model->getArrayCopy();
        if (isset($model->id)) {
            $result = $this->gateway->update($set, $where, $joins);
        } else {
            $result = $this->gateway->insert($set);
        }
        return $result;
    }

    public function fetchAllWithChildren(): ResultSetInterface|array
    {

        $categories = $this->gateway->select()->toArray();
        $count = count($categories);

        for ($i=0; $i < $count; $i++) {
            $children = $this->fetchChildren($categories[$i]['id'])->toArray();
            if (count($children) >= 1) {
                $categories[$i]['children'] = $children;
            } else {
                $categories[$i]['children'] = null;
            }
        }
        return $categories;
    }

    public function fetchChildren($parentId)
    {
        /** @var Sql $sql */
        $sql = $this->gateway->getSql();
        $select = $sql->select();
        $select->where(function(Where $where) use ($parentId) {
            $where->equalTo('parentId', $parentId);
        });
        return $this->gateway->selectWith($select);
    }
}
