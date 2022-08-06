<?php

declare(strict_types=1);

namespace ContentManager\Db\Factory;

use ContentManager\Db\Listener\PageGatewayListener;
use ContentManager\Db\PageGateway;
use ContentManager\Model\Page;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class PageGatewayFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageGateway
    {
        $config             = $container->get('config');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(
            new Page()
        );
        return new $requestedName(
            $config['db']['pages_table_name'],
            $container->get('EventManager'), // This must use the string name not the class-string
            $resultSetPrototype,
            true,
            $container->get(PageGatewayListener::class)
        );
    }
}
