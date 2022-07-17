<?Php

declare(strict_types=1);

namespace App\Form\Fieldset\Factory;

use App\Form\Fieldset\SecurityFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class SecurityFieldsetFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SecurityFieldset
    {
        return new SecurityFieldset($container->get('config')['app_settings'], $options);
    }
}
