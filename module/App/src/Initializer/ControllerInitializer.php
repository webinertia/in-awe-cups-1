<?php

declare(strict_types=1);

namespace App\Initializer;

use App\Controller\ControllerInterface;
use App\Service\AppSettingsAwareInterface;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Psr\Container\ContainerInterface;
use User\Acl\AclAwareInterface;
use User\Service\UserService;
use User\Service\UserServiceAwareInterface;

class ControllerInitializer implements InitializerInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (! $instance instanceof ControllerManager) {
            return;
        }
        $instance->addInitializer([$this, 'initializeController']);
    }

    /** @param ControllerInterface $instance */
    public function initializeController(ContainerInterface $container, $instance)
    {
        if (! $instance instanceof ControllerInterface) {
            return;
        }
        if ($instance instanceof AclAwareInterface) {
            $instance->setAcl($container->get(AclInterface::class));
        }
        if ($instance instanceof AppSettingsAwareInterface) {
            $instance->setAppSettings($container->get('config')['app_settings']);
        }
        if ($instance instanceof UserServiceAwareInterface) {
            $instance->setUserService($container->get(UserService::class));
        }
    }
}
