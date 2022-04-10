<?php

declare(strict_types=1);

namespace ContentManager\Form\Fieldset\Factory;

use ContentManager\Form\Fieldset\PageFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class PageFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageFieldset
    {
        return new PageFieldset($options);
    }
}
