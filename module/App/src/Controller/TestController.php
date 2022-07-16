<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use Laminas\View\Model\ViewModel;
use Webinertia\Utils\Debug;

final class TestController extends AbstractAppController
{
    //protected $resourceId = 'test-controller';
    public function indexAction(): ViewModel
    {
        Debug::dump($this->getResourceId());
        $log = $this->getLogger();
        $limit = $this->params()->fromQuery('limit');
        if($limit > 0) {
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
