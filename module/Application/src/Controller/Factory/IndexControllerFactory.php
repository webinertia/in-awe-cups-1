<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Form\ContactForm;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $formManager = $container->get(FormElementManager::class);
        return new IndexController($formManager->get(ContactForm::class));
    }
}
