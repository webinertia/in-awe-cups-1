<?php

declare(strict_types=1);

namespace ContentManager\Form\Factory;

use ContentManager\Form\PageForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Db\UserGateway;

final class PageFormFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageForm
    {
        return new PageForm(
            $container->get(UserGateway::class),
            $container->get('config')['app_settings'],
            $options
        );
    }
}
