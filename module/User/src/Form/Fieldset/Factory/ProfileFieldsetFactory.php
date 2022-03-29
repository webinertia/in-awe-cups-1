<?php 
declare(strict_types=1);
namespace User\Form\Fieldset\Factory;

use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Form\Fieldset\ProfileFieldset;

class ProfileFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ProfileFieldset($container->get(Settings::class));
    }
}