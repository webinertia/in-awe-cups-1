<?php

declare(strict_types=1);

namespace Application\Db\TableGateway\Factory;

use Application\Db\TableGateway\SettingsTable;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SettingsTableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new SettingsTable('settings', $container);
    }
}
