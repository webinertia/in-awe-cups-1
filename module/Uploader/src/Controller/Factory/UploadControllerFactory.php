<?php

declare(strict_types=1);

namespace Uploader\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Uploader\Controller\UploadController;
use Uploader\Uploader;

class UploadControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new UploadController($container->get(Uploader::class));
    }
}
