<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterPluginManager;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\ProductOptionsTable;
use Store\Model\ProductOptions;

class ProductOptionsFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProductOptions
    {
       // $filterFactory = new Factory($container->get(InputFilterPluginManager::class));
        return new $requestedName(
            $container->get(ProductOptionsTable::class)
        );
    }
}
