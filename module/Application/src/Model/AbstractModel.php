<?php

declare(strict_types=1);

namespace Application\Model;

use Application\Db\TableGateway\TableGateway;
use Laminas\Config\Config;
use Laminas\Db\ResultSet\Exception\InvalidArgumentException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Laminas\Permissions\Acl\ProprietaryInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Laminas\Stdlib\ArrayObject;
use Webinertia\ModelManager\ModelInterface;

abstract class AbstractModel extends ArrayObject implements
    ResourceInterface,
    ProprietaryInterface,
    RoleInterface,
    ModelInterface
{
    /** @var TableGateway $db; */
    protected $db;
    /** @var Config $config */
    protected $config;
    /** @var Logger $logger */
    /**
     * @param string $table
     * @return void
     * @throws InvalidArgumentException
     * @throws ExceptionInvalidArgumentException
     */
    public function __construct($table, EventManager $eventManager, Config $config, ?Logger $logger = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype($this);
        $this->db     = new TableGateway($table, $eventManager, $resultSetPrototype);
        $this->config = $config;
        parent::__construct([], ArrayObject::ARRAY_AS_PROPS);
    }

    public function getTablegateway(): TableGateway
    {
        return $this->db;
    }

    public function getRoleId(): string
    {
        if ($this->offsetExists('role')) {
            return $this->offsetGet('role');
        }
        return null;
    }

    public function getOwnerId(): int
    {
        /**
         * userId is always the foriegn key that points to
         * users.id
         */
        if ($this->offsetExists('userId')) {
            return $this->offsetGet('userId');
        }
    }

    public function getResourceId(): string
    {
        return $this->db->getTable();
    }

    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    public function getSql(): Sql
    {
        return $this->db->getSql();
    }

    public function getResultSetPrototype(): ResultSet
    {
        return $this->db->getResultSetPrototype();
    }
}
