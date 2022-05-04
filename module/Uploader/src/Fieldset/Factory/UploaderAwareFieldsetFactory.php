<?php

declare(strict_types=1);

namespace Uploader\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Uploader\Fieldset\UploaderAwareFieldset;

class UploaderAwareFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): UploaderAwareFieldset {
        return new UploaderAwareFieldset($options);
    }
}
