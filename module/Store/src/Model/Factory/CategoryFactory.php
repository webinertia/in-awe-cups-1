<?php

declare(strict_types=1);

namespace Store\Model\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Store\Db\TableGateway\CategoriesTable;
use Store\Model\Category;

class CategoryFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Category
    {
        return new $requestedName($container->get(CategoriesTable::class));
    }
}
