<?Php

declare(strict_types=1);

namespace Application\Form\Fieldset\Factory;

use Application\Form\Fieldset\SecurityFieldset;
use Application\Model\Settings;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Webinertia\ModelManager\ModelManager;

class SecurityFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): SecurityFieldset
    {
        return new SecurityFieldset($container->get(ModelManager::class)->get(Settings::class), $options);
    }
}
