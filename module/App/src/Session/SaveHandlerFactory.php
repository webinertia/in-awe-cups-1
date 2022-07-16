<?php

declare(strict_types=1);

namespace App\Session;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class SaveHandlerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): DbTableGateway
    {
        $config = $container->get('config')['db'];
        // db options
        $dbOptions = [
            'idColumn'       => 'id',
            'nameColumn'     => 'name',
            'modifiedColumn' => 'modified',
            'lifetimeColumn' => 'lifetime',
            'dataColumn'     => 'data',
        ];
        return new DbTableGateway(
            new TableGateway($config['sessions_table_name'], $container->get(AdapterInterface::class)),
            new DbTableGatewayOptions($dbOptions)
        );
    }
}
