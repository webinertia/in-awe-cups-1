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
use Webinertia\Utils\Debug;

use function array_merge_recursive;

final class TestController extends AbstractAppController
{
    /** @var UserForm $form */
    protected $form;

    public function indexAction(): ViewModel
    {
        $config = $this->service()->get('config')['app_settings'];
        if ($config['server']['enable_error_log']) {
            Debug::dump($config);
        }
        return $this->view;
    }
}
