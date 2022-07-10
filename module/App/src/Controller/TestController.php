<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;
use User\Form\UserForm;
use User\Model\Users;
use Laminas\Log\Logger;
use Webinertia\Utils\Debug;
use Psr\Log\LoggerInterface;

use function array_merge_recursive;

final class TestController extends AbstractAppController
{
    /** @var UserForm $form */
    protected $form;

    public function indexAction(): ViewModel
    {
        $appSettings = $this->service()->get('config')['app_settings'];
        $logger = $this->service()->get(LoggerInterface::class);
        $logger->info('User {firstName} {lastName} logged an action');
        $config      = $this->service()->get('config');
        if ($appSettings['server']['enable_error_log']) {
            //Debug::dump($config['psr_log']);
        }
        return $this->view;
    }
}
