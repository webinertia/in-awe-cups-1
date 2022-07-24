<?php

declare(strict_types=1);

namespace App\Service\Factory;

use App\Service\Email;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class EmailFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Email
    {
        return new Email($container->get('config'));
    }
}
