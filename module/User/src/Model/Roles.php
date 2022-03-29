<?php

declare(strict_types=1);

namespace User\Model;

use Application\Model\AbstractModel;
use Application\Model\ModelTrait;
use Laminas\Db\Sql\Exception\InvalidArgumentException;
use Laminas\Db\TableGateway\Exception\RuntimeException as ExceptionRuntimeException;
use RuntimeException;

use function count;

class Roles extends AbstractModel
{
    use ModelTrait;

    /**
     * @return array
     * @throws ExceptionRuntimeException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function fetchSelectData(): array
    {
        $data   = [];
        $result = $this->db->select();
        foreach ($result as $row) {
            $rowData              = $row->getArrayCopy();
            $data[$rowData['id']] = [
                'value' => $rowData['role'],
                'label' => $rowData['label'],
            ];
        }
        if (count($data) > 0) {
            return $data;
        } else {
            throw new RuntimeException('Roles could not be retrieved');
        }
    }
}
