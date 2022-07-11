<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;

final class TestController extends AbstractAppController
{
    public function indexAction(): ViewModel
    {
        return $this->view;
    }
}
