<?php

declare(strict_types=1);

namespace Uploader\Adapter;

use Laminas\Db\Adapter\AdapterInterface as DbAdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use RuntimeException;
use Throwable;
use Uploader\Adapter\AbstractAdapter;
use User\Model\UserTable;

use function array_flip;
use function array_key_exists;
use function compact;
use function is_array;

class TableGatewayAdapter extends AbstractAdapter
{
        /**
         * Primary table that records should be stored in.
         *
         * @var TableGateway $table
         */
    protected $table;
    /** @var TableGateway $relatedTable */
    protected $relatedTable;
    /**
     * In nearly call cases we should store a userId who uploads this image so they can be tracked
     *
     * @var UserTable $userTable
     */
    protected $userTable;
    /**
     * @return void
     */
    public function __construct(
        DbAdapterInterface $dbAdapter,
        array $config,
        EventManager $eventManager,
        ?Logger $logger = null
    ) {
        $this->dbAdapter = $dbAdapter;
        $this->getConfig($config);
        $this->setEventManager($eventManager);
        if ($logger instanceof Logger) {
            $this->logger = $logger;
        }
    }

    public function loadContext(): void
    {
        parent::loadContext();
        if (isset($this->config[$this->module]['db_config']['table_name'])) {
            $this->table = new TableGateway($this->config[$this->module]['db_config']['table_name'], $this->dbAdapter);
        }
    }

    public function upload(): void
    {
        // make sure $this->setData($data) has been called in user land
        if (! empty($this->data) && is_array($this->data['file'])) {
            $uploaded = $this->handler->filter($this->data['file']);
            unset($this->data['file']);
            $this->baseName = $this->filter->filter($uploaded['tmp_name']);
        }
        if ($this->table instanceof TableGateway) {
            // apparently we need to store a record for this image
            try {
                // first lets find the relevant data
                //unset($file);// were done with this so lets clean up alittle
                $dbConfig = $this->config[$this->module]['db_config'];
                $data     = [];
                // what were saving
                // does the config tell us which columns we need? it should!!!!
                if (! is_array($dbConfig['columns'])) {
                    throw new RuntimeException('Missing columns index in uploader configuration!!');
                }
                $columns = array_flip($dbConfig['columns']);
                if (isset($columns[$dbConfig['image_column']])) {
                    $data[$dbConfig['image_column']] = $this->baseName;
                }
                if (isset($this->data['upload-config'])) {
                    unset($this->data['upload-config']);
                }
                if (isset($this->data['file'])) {
                    unset($this->data['file']);
                }

                foreach ($this->data as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if (array_key_exists($k, $columns) && $k !== $dbConfig['image_column']) {
                                            $data[$k] = $value[$k];
                                            continue;
                            }
                        }
                    } else {
                        if (isset($columns[$key]) && $key !== $dbConfig['image_column']) {
                            $data[$key] = $value;
                        }
                    }
                }
                $this->table->insert($data);
                $module   = $this->module;
                $baseName = $this->baseName;
                $params   = [$module, $baseName];
                $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
            } catch (Throwable $th) {
                $this->logger->log(Logger::ERR, $th->getMessage());
            }
        }
    }
}
