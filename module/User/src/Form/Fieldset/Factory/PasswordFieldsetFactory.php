<?Php 
declare(strict_types=1);
namespace User\Form\Fieldset\Factory;

use Application\Model\Settings;
use Interop\Container\ContainerInterface;
use Laminas\Form\Fieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Form\Fieldset\PasswordFieldset;

class PasswordFieldsetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $settings = $container->get(Settings::class);
        return new PasswordFieldset($settings);
    }
}