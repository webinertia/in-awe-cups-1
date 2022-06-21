<?php

declare(strict_types=1);

namespace ContentManager\Navigation;

use ContentManager\Navigation\Page\AbstractPage;
use Laminas\Navigation\AbstractContainer as LaminasContainer;
use Laminas\Navigation\Exception\InvalidArgumentException;
use Traversable;

use function array_key_exists;
use function is_array;

abstract class AbstractContainer extends LaminasContainer
{
    /**
     * Adds a page to the container
     *
     * This method will inject the container as the given page's parent by
     * calling {@link Page\AbstractPage::setParent()}.
     *
     * @param  AbstractPage|array|Traversable $page  page to add
     * @return self fluent interface, returns self
     * @throws Exception\InvalidArgumentException If page is invalid.
     */
    public function addPage($page)
    {
        if ($page === $this) {
            throw new InvalidArgumentException(
                'A page cannot have itself as a parent'
            );
        }

        if (! $page instanceof AbstractPage) {
            if (! is_array($page) && ! $page instanceof Traversable) {
                throw new InvalidArgumentException(
                    'Invalid argument: $page must be an instance of '
                    . 'Laminas\Navigation\Page\AbstractPage or Traversable, or an array'
                );
            }
            $page = AbstractPage::factory($page);
        }

        $hash = $page->hashCode();

        if (array_key_exists($hash, $this->index)) {
            // page is already in container
            return $this;
        }

        // adds page to container and sets dirty flag
        $this->pages[$hash] = $page;
        $this->index[$hash] = $page->getOrder();
        $this->dirtyIndex   = true;

        // inject self as page parent
        $page->setParent($this);

        return $this;
    }
}
