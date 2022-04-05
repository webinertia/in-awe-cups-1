<?Php

declare(strict_types=1);

namespace User\Model\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Config\Config;
use Laminas\EventManager\EventManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Model\Roles;
use Webinertia\ModelManager\ModelManager;

class RolesFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Roles
    {
        $config = new Config($container->get('Config'));
        return new Roles(
            $config->db->user_roles_table_name,
            $container->get(ModelManager::class),
            $container->get(EventManager::class),
            $config
        );
    }
}
