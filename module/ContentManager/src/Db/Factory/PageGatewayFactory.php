<?php

declare(strict_types=1);

namespace ContentManager\Db\Factory;

use App\Db\TableGateway\AbstractGatewayModel;
use ContentManager\Db\PageGateway;
use ContentManager\Model\Page;
use Laminas\Db\ResultSet\Exception\InvalidArgumentException;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class PageGatewayFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageGateway
    {
        $config             = $container->get('config');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(
            new Page([], AbstractGatewayModel::ARRAY_AS_PROPS)
        );
        return new PageGateway($config['db']['pages_table_name'], null, $resultSetPrototype);
    }
}
