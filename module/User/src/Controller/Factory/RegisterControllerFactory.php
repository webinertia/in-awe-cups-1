<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use App\Form\FormInterface;
use App\Model\Settings;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\RegisterController;
use User\Form\UserForm;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

class RegisterControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RegisterController
    {
        $fm = $container->get(FormElementManager::class);
        return new RegisterController(
            $container->get(ModelManager::class)->get(Users::class),
            $fm->build(UserForm::class, ['mode' => FormInterface::CREATE_MODE]),
            $container->get(ModelManager::class)->get(Settings::class)
        );
    }
}
