<?php

declare(strict_types=1);

namespace ContentManager\Form\Factory;

use App\Model\Settings;
use ContentManager\Form\PageForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Db\UserGateway;

final class PageFormFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PageForm
    {
        return new PageForm(
            $container->get(UserGateway::class),
            $container->get(Settings::class),
            $options
        );
    }
}