<?php

declare(strict_types=1);

namespace App\View\Helper\Service;

use App\View\Helper\IconifiedControl;
use Laminas\ServiceManager\ServiceManager;

class IconifiedControlFactory
{
    public function __invoke(ServiceManager $container): IconifiedControl
    {
        if (! $container instanceof ServiceManager) {
            $container = $container->get('ViewHelperManager');
        }
        return new IconifiedControl($container->get('Acl'));
    }
}
