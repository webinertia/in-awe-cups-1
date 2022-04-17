<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\AdminController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class AdminControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AdminController
    {
        return new AdminController();
    }
}
