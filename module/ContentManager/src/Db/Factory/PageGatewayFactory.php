<?php

declare(strict_types=1);

namespace ContentManager\Db\Factory;

use ContentManager\Db\Listener\InsertUpdateListener;
use ContentManager\Db\PageGateway;
use ContentManager\Model\Page;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\EventManager\EventManager;
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
        return new PageGateway(
            $config['db']['pages_table_name'],
            $container->get(EventManager::class),
            $resultSetPrototype,
            true,
            $container->get(InsertUpdateListener::class)
        );
    }
}
