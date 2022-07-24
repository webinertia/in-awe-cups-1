<?php

declare(strict_types=1);

namespace ContentManager\Model\Factory;

use ContentManager\Db\PageGateway;
use ContentManager\Model\Page;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class PageFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Page
    {
        return new $requestedName(
            $container->get(PageGateway::class)
        );
    }
}
