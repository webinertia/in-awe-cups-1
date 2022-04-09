<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Form\ContactForm;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class IndexControllerFactory implements FactoryInterface
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
