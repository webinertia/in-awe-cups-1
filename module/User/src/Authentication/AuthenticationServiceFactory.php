<?php

declare(strict_types=1);

namespace User\Authentication;

use Laminas\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function password_verify;

final class AuthenticationServiceFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): AuthenticationService {
        $authConfig      = $container->get('config')['db'];
        $databaseAdapter = $container->get(AdapterInterface::class);
        Assert::isInstanceOf($databaseAdapter, Adapter::class);

        $credentialValidationCallback =
            static function (
                string $hash,
                string $password
            ): bool {
                return password_verify($password, $hash);
            };

        $authAdapter = new CallbackCheckAdapter(
            $databaseAdapter,
            $authConfig['users_table_name'], // table name
            $authConfig['auth_identity_column'], // identity column
            $authConfig['auth_credential_column'], // credential column
            $credentialValidationCallback
        );
        $select      = $authAdapter->getDbSelect();
        $select->where('active = 1');
        return new $requestedName(
            new Session('Aurora_Auth', null, $container->get(SessionManager::class)),
            $authAdapter
        );
    }
}
