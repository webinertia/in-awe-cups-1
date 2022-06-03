<?php

declare(strict_types=1);

namespace Uploader;

use Laminas\ServiceManager\AbstractPluginManager;
use Uploader\Adapter\AdapterInterface;
use Uploader\Adapter\Factory\TableGatewayAdapterFactory;
use Uploader\Adapter\TableGatewayAdapter;

final class AdapterPluginManager extends AbstractPluginManager
{
    /** @var array $factories */
    protected $factories = [
        TableGatewayAdapter::class => TableGatewayAdapterFactory::class,
    ];
    /** @var string */
    protected $instanceOf = AdapterInterface::class;
}
