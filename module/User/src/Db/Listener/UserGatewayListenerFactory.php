<?php

declare(strict_types=1);

namespace User\Db\Listener;

use App\Service\Email;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use User\Db\Listener\UserGatewayListener;

final class UserGatewayListenerFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): UserGatewayListener {
        return new $requestedName(
            $container->get(Email::class),
            $container->get('config')['app_settings']['server']['time_format']
        );
    }
}
