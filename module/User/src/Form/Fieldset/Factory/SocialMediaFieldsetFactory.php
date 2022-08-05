<?php

declare(strict_types=1);

namespace User\Form\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Form\Fieldset\SocialMediaFieldset;

final class SocialMediaFieldsetFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = []): SocialMediaFieldset
    {
        $config = $container->get('config');
        return new $requestedName(options:$options, appSettings:$config['app_settings']);
    }
}
