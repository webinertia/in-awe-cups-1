<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\ContactForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class ContactFormFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ContactForm
    {
        return new ContactForm($container->get('config')['app_settings']);
    }
}
