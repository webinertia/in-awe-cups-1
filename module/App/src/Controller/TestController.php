<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 * phpcs:ignoreFile
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareInterfaceTrait;
use App\Log\LogEvent;
use Laminas\View\Model\ViewModel;
use User\Acl\ResourceAwareTrait;
use Webinertia\Utils\Debug;

final class TestController extends AbstractAppController implements LoggerAwareInterface
{
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';
    public function indexAction(): ViewModel
    {
        Debug::dump($this->params()->fromQuery());
        $settings = $this->getService('config')['app_settings'];
        $appSettings    = $this->getService('config')['app_settings'];
        $moduleSettings = $this->getService('config')['module_settings']['user'];

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost()->toArray();
            Debug::dump($data);
        }

        $this->getEventManager()->trigger(LogEvent::NOTICE, 'This is a standard log message');

        $this->getEventManager()->trigger(LogEvent::ALERT, 'log_login_success');

        $limit = $this->params()->fromQuery('limit');
        if ($limit > 0) {
            for ($i = 0; $i < $limit; $i++) {
                $this->getEventManager()->trigger(LogEvent::DEBUG, 'Auto generated log message number ' . $i);
            }
        }
        return $this->view;
    }
}
