<?php

declare(strict_types=1);

namespace Application\Service\Factory;

use Application\Service\Email;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class EmailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new Email($container->get('config'));
    }
}
