<?php

declare(strict_types=1);

namespace App\Db\DbGateway\Factory;

use App\Db\DbGateway\LogGateway;
use App\Model\LogMessage;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class LogGatewayFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LogGateway
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new LogMessage());
        return new $requestedName('log', null, $resultSetPrototype);
    }
}
