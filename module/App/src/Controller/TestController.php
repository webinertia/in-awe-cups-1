<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 * phpcs:ignoreFile
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;
use User\Acl\ResourceAwareTrait;
use Webinertia\Utils\Debug;

final class TestController extends AbstractAppController
{
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';

    public function indexAction(): ViewModel
    {
        $settings = $this->service('config')['app_settings'];
        $appSettings    = $this->service('config')['app_settings'];
        $moduleSettings = $this->service('config')['module_settings']['user'];

        Debug::dump($appSettings['server']['upload_basepath'] . $moduleSettings['server']['profile_image_target_path']);
        $limit = $this->params()->fromQuery('limit');
        if ($limit > 0) {
            $this->warning('This is a warning');
            for ($i = 0; $i < $limit; $i++) {
                $this->info("This is a test log message $i");
            }
            $this->error('This is an error');
            $this->critical('This is a critical error');
            $this->alert('This is an alert');
            $this->emergency('This is an emergency');
        }
        return $this->view;
    }
}
