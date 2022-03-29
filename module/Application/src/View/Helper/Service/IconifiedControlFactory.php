<?php

declare(strict_types=1);

namespace Application\View\Helper\Service;

use Application\View\Helper\IconifiedControl;
use Laminas\ServiceManager\ServiceManager;

class IconifiedControlFactory
{
    public function __invoke(ServiceManager $container)
    {
        if (! $container instanceof ServiceManager) {
            $container = $container->get('ViewHelperManager');
        }
        return new IconifiedControl($container->get('Acl'));
    }
}
