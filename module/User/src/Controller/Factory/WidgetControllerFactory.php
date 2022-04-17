<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Controller\WidgetController;

class WidgetControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): WidgetController
    {
        return new WidgetController();
    }
}
