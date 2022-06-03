<?php

declare(strict_types=1);

namespace App\Controller\Factory;

use App\Controller\IndexController;
use App\Form\ContactForm;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): IndexController
    {
        $formManager = $container->get(FormElementManager::class);
        return new IndexController($formManager->get(ContactForm::class));
    }
}
