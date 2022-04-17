<?php

declare(strict_types=1);

namespace Uploader\Fieldset\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Uploader\Fieldset\UploaderAwareMultiFile;

class UploaderAwareMultiFileFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): UploaderAwareMultiFile {
        return new UploaderAwareMultiFile();
    }
}
