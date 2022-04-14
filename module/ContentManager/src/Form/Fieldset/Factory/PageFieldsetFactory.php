<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset\Factory;

use ContentManager\Form\Fieldset\PageFieldset;
use ContentManager\Model\Pages;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Webinertia\ModelManager\ModelManager;

final class PageFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageFieldset
    {
        return new PageFieldset($container->get(ModelManager::class)->get(Pages::class), $options);
    }
}
