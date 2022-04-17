<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use Application\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Fieldset\LoginFieldset;
use Webinertia\ModelManager\ModelManager;

class LoginFieldsetFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginFieldset
    {
        return new LoginFieldset(($container->get(ModelManager::class))->get(Settings::class));
    }
}
