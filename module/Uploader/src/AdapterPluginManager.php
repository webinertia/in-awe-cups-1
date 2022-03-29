<?php

declare(strict_types=1);

namespace Uploader;

use Laminas\ServiceManager\AbstractPluginManager;
use Uploader\Adapter\AdapterInterface;
use Uploader\Adapter\Factory\TableGatewayAdapterFactory;
use Uploader\Adapter\TableGatewayAdapter;

class AdapterPluginManager extends AbstractPluginManager
{
    protected $factories = [
        TableGatewayAdapter::class => TableGatewayAdapterFactory::class,
    ];
/** @var string */
    protected $instanceOf = AdapterInterface::class;

    // public function configure(array $config)
    // {
    //     if (isset($config['services'])) {
    //         /** @psalm-suppress MixedAssignment */
    //         foreach ($config['services'] as $service) {
    //             $this->validate($service);
    //         }
    //     }
    //     parent::configure($config);
    //     return $this;
    // }
}
