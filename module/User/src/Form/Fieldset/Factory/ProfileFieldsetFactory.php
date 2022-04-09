<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use Application\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Fieldset\ProfileFieldset;
use Webinertia\ModelManager\ModelManager;

class ProfileFieldsetFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProfileFieldset
    {
        return new ProfileFieldset(($container->get(ModelManager::class))->get(Settings::class));
    }
}