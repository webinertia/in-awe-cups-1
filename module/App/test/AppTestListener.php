<?php

declare(strict_types=1);

namespace AppTest;

use AppTest\Platform\FixtureLoader;
use AppTest\Platform\MysqlFixtureLoader;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\TestHook;

use function getenv;
use function printf;

class AppTestListener implements TestHook, TestListener
{
    use TestListenerDefaultImplementation;

    /** @var FixtureLoader[] */
    private $fixtureLoaders = [];

    public function startTestSuite(TestSuite $suite): void
    {
        // if ($suite->getName() !== 'integration test') {
        //     return;
        // }

        if (getenv('DB_ADAPTER_DRIVER_MYSQL')) {
            $this->fixtureLoaders[] = new MysqlFixtureLoader();
        }

        // if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL')) {
        //     $this->fixtureLoaders[] = new PgsqlFixtureLoader();
        // }

        // if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_SQLSRV')) {
        //     $this->fixtureLoaders[] = new SqlServerFixtureLoader();
        // }

        if (empty($this->fixtureLoaders)) {
            return;
        }

        printf("\nIntegration test started.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->createDatabase();
        }
    }

    public function endTestSuite(TestSuite $suite): void
    {
        if (
            $suite->getName() !== 'integration test'
            || empty($this->fixtureLoader)
        ) {
            return;
        }

        printf("\nIntegration test ended.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->dropDatabase();
        }
    }
}
