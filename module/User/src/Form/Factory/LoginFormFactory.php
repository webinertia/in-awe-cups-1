<?php

declare(strict_types=1);

namespace User\Form\Factory;

use App\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Form\LoginForm;
use Webinertia\ModelManager\ModelManager;

class LoginFormFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LoginForm
    {
        return new LoginForm($container->get(ModelManager::class)->get(Settings::class));
    }
}
