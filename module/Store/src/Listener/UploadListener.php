<?php

declare(strict_types=1);

namespace Store\Listener;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManager;
use Uploader\Adapter\TableGatewayAdapter;
use Uploader\Adapter\AdapterInterface;

class UploadListener extends AbstractListenerAggregate
{
    public function __construct() {}

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(TableGatewayAdapter::EVENT_UPLOAD, function($e)  {
            $event = $e->getName();
            $target = get_class($e->getTarget());
            $params = $e->getParams();
        });
    }
    public function getPath(TableGatewayAdapter $event) : string
    {
        //
        $publicPath = $event->getPublicPath();
        return $publicPath;
    }
}