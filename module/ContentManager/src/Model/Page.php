<?php

declare(strict_types=1);

namespace ContentManager\Model;

use ContentManager\Model\Pages;
use Laminas\Navigation\Page\Mvc;
use Webinertia\ModelManager\ModelInterface;

final class Page implements ModelInterface
{
    /** @var Pages $pageModel */
    protected $pageModel;
    /** @var Mvc $mvcPage */
    protected $mvcPage;
    /** @return void */
    public function __construct()
    {
    }
}
