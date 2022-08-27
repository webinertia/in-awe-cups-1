<?php

declare(strict_types=1);

namespace User\Command\Factory;

use App\Service\Email;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Command\CreateUserCommand;

class CreateUserCommandFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CreateUserCommand
    {
        return new $requestedName($container->get(AdapterInterface::class), $container->get('config'));
    }
}
