<?php

/**
 * based on code by https://github.com/froschdesign
 */

declare(strict_types=1);

namespace User\Navigation\View;

use Laminas\Permissions\Acl\AclInterface;
use Laminas\View\Helper\Navigation\AbstractHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PermissionAclDelegatorFactory
{
    /** @var string */
    private $aclName;

    public function __construct(string $aclName = AclInterface::class)
    {
        $this->aclName = $aclName;
    }

    public static function __set_state(array $state): self
    {
        return new self($state['aclName'] ?? AclInterface::class);
    }

    /**
     * @param null|array $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        ?array $options = null
    ): AbstractHelper {
        $helper = $callback();

        if (! $helper instanceof AbstractHelper) {
            return $helper;
        }
        if (! $container->has($this->aclName)) {
            return $helper;
        }

        $acl = $container->get($this->aclName);
        if (! $acl instanceof AclInterface) {
            return $helper;
        }

        $helper->setAcl($acl);

        return $helper;
    }
}
