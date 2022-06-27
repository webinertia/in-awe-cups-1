<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use App\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Fieldset\LoginFieldset;

final class LoginFieldsetFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginFieldset
    {
        return new LoginFieldset($container->get(Settings::class));
    }
}
