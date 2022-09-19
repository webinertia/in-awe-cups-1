<?php

declare(strict_types=1);

namespace AppTest\Platform;

use Exception;
use PDO;

use function file_get_contents;
use function getenv;
use function print_r;
use function sprintf;

class MysqlFixtureLoader implements FixtureLoader
{
    /** @var string */
    private $fixtureFile = __DIR__ . '/../Fixtures/mysql.sql';

    /** @var PDO */
    private $pdo;

    public function createDatabase()
    {
        $this->connect();

        if (
            false === $this->pdo->exec(sprintf(
                "CREATE DATABASE IF NOT EXISTS %s",
                getenv('DB_ADAPTER_DRIVER_MYSQL_DATABASE')
            ))
        ) {
            throw new Exception(sprintf(
                "I cannot create the MySQL %s test database: %s",
                getenv('DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->pdo->exec('USE ' . getenv('DB_ADAPTER_DRIVER_MYSQL_DATABASE'));

        if (false === $this->pdo->exec(file_get_contents($this->fixtureFile))) {
            throw new Exception(sprintf(
                "I cannot create the table for %s database. Check the %s file. %s ",
                getenv('DB_ADAPTER_DRIVER_MYSQL_DATABASE'),
                $this->fixtureFile,
                print_r($this->pdo->errorInfo(), true)
            ));
        }

        $this->disconnect();
    }

    public function dropDatabase()
    {
        $this->connect();

        $this->pdo->exec(sprintf(
            "DROP DATABASE IF EXISTS %s",
            getenv('DB_ADAPTER_DRIVER_MYSQL_DATABASE')
        ));

        $this->disconnect();
    }

    protected function connect()
    {
        $dsn = 'mysql:host=' . getenv('DB_ADAPTER_DRIVER_MYSQL_HOSTNAME');
        if (getenv('DB_ADAPTER_DRIVER_MYSQL_PORT')) {
            $dsn .= ';port=' . getenv('DB_ADAPTER_DRIVER_MYSQL_PORT');
        }

        $this->pdo = new PDO(
            $dsn,
            getenv('DB_ADAPTER_DRIVER_MYSQL_USERNAME'),
            getenv('DB_ADAPTER_DRIVER_MYSQL_PASSWORD')
        );
    }

    protected function disconnect()
    {
        $this->pdo = null;
    }
}
