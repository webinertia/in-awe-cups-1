<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use Application\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Fieldset\PasswordFieldset;
use Webinertia\ModelManager\ModelManager;

class PasswordFieldsetFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordFieldset
    {
        return new PasswordFieldset(($container->get(ModelManager::class))->get(Settings::class));
    }
}