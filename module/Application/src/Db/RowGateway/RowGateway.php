<?php

declare(strict_types=1);

namespace Application\Db\RowGateway;

use Laminas\Db\RowGateway\RowGateway as LaminasRowGateWay;
use Laminas\Permissions\Acl\ProprietaryInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class RowGateway extends LaminasRowGateWay implements RoleInterface, ResourceInterface, ProprietaryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\Permissions\Acl\Resource\ResourceInterface::getResourceId()
     */
    public function getResourceId()
    {
        return $this->table;
    }

    public function getArrayCopy()
    {
        return $this->toArray();
    }

    /**
     * @see \Laminas\Permissions\Acl\ProprietaryInterface::getOwnerId()
     */
    public function getOwnerId()
    {
        // TODO Auto-generated method stub
        if ($this->offsetExists('userId')) {
            return $this->offsetGet('userId');
        }
        return $this->offsetGet('id');
    }

    /**
     * @see \Laminas\Permissions\Acl\Role\RoleInterface::getRoleId()
     */
    public function getRoleId()
    {
        // TODO Auto-generated method stub
        return $this->offsetGet('role');
    }
}
