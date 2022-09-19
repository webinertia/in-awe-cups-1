<?php

declare(strict_types=1);

namespace AppTest\Platform;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface FixtureLoader
{
    public function createDatabase();

    public function dropDatabase();
}
