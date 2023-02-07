<?php

declare(strict_types=1);

namespace Store\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Store\Model\OptionsPerProduct;

final class ProductCount extends AbstractHelper
{
    /** @var OptionsPerProduct $lookup */
    protected $lookup;

    public function __construct(OptionsPerProduct $lookup)
    {
        $this->lookup = $lookup;
    }

    public function __invoke(string $option)
    {
        return $this->lookup->fetchProductCountByOption($option);
    }
}
