<?php

declare(strict_types=1);

namespace Uploader\Fieldset\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Uploader\Fieldset\UploaderAwareMultiFile;

class UploaderAwareMultiFileFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new UploaderAwareMultiFile();
    }
}
