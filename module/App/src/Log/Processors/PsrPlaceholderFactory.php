<?php

declare(strict_types=1);

namespace App\Log\Processors;

use App\Log\Processors\PsrPlaceholder;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Service\UserInterface;

final class PsrPlaceholderFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): PsrPlaceholder {
        return new $requestedName($container->get(UserInterface::class));
    }
}