<?Php

declare(strict_types=1);

namespace Application\Form\Fieldset\Factory;

use Application\Form\Fieldset\SecurityFieldset;
use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SecurityFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SecurityFieldset
    {
        return new SecurityFieldset($container->get(Settings::class), $options);
    }
}
