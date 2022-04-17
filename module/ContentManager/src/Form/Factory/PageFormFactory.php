<?php

declare(strict_types=1);

namespace ContentManager\Form\Factory;

use Application\Model\Settings;
use ContentManager\Form\PageForm;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Permissions\PermissionsManager;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

final class PageFormFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageForm
    {
        return new PageForm(
            $container->get(AuthenticationService::class),
            $container->get(PermissionsManager::class),
            $container->get(ModelManager::class)->get(Users::class),
            $container->get(ModelManager::class)->get(Settings::class),
            $options
        );
    }
}