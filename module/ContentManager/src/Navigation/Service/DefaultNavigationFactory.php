<?php

declare(strict_types=1);

namespace ContentManager\Navigation\Service;

use ContentManager\Model\Pages;
use ContentManager\Navigation\Navigation;
use Laminas\Navigation\Exception\InvalidArgumentException;
use Laminas\Navigation\Service\AbstractNavigationFactory;
use Psr\Container\ContainerInterface;
use Webinertia\ModelManager\ModelManager;

use function array_merge;
use function sprintf;

/**
 * Default navigation factory.
 */
final class DefaultNavigationFactory extends AbstractNavigationFactory
{
    /** @var ModelManager $modelManager */
    protected $modelManager;
    /** @var Pages $pages */
    protected $pageModel;
    /** @var array $pageMenu */
    protected $pageMenu = [];
    /**
     * Create and return a new Navigation instance (v3).
     *
     * @param string $requestedName
     * @param null|array $options
     * @return Navigation
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $this->modelManager = $container->get(ModelManager::class);
        $this->pageModel    = $this->modelManager->get(Pages::class);
        $this->pageMenu     = $this->pageModel->fetchMenu();
        return new Navigation($this->getPages($container));
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    protected function getPages(ContainerInterface $container)
    {
        if (null === $this->pages) {
            $configuration = $container->get('config');
            if (! isset($configuration['navigation'])) {
                throw new InvalidArgumentException('Could not find navigation configuration key');
            }
            if (! isset($configuration['navigation'][$this->getName()])) {
                throw new InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }
            $pages       = array_merge(
                $this->getPagesFromConfig(
                    $configuration['navigation'][$this->getName()]
                ),
                $this->pageMenu
            );
            $this->pages = $this->preparePages($container, $pages);
        }
        return $this->pages;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'default';
    }
}
