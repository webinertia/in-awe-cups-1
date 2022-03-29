<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\RegisterController;
use User\Form\UserForm;
use User\Model\Users;

class RegisterControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $fm = $container->get(FormElementManager::class);
//$settings = new Config($container->get('Config'));
        return new RegisterController($container->get(Users::class), $fm->build(UserForm::class, ['mode' => UserForm::CREATE_MODE]), $container->get(Settings::class));
    }
}
