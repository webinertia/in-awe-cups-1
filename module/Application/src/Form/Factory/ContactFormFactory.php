<?php

declare(strict_types=1);

namespace Application\Form\Factory;

use Application\Form\ContactForm;
use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ContactFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ContactForm($container->get(Settings::class));
    }
}
