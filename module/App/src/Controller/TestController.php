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
        $log   = $this->getLogger();
        $limit = $this->params()->fromQuery('limit');
        if ($limit > 0) {
            $log->warning('This is a warning');
            for ($i = 0; $i < $limit; $i++) {
                $log->info("This is a test log message $i");
            }
            $log->error('This is an error');
            $log->critical('This is a critical error');
            $log->alert('This is an alert');
            $log->emergency('This is an emergency');
        }
        return $this->view;
    }
}
