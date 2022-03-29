<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Application\Model\Settings;
use Laminas\View\Model\ViewModel;
use User\Model\Users;

class AccountController extends AbstractController
{
    public function __construct(Users $usrModel, Settings $appSettings)
    {
        $this->usrModel    = $usrModel;
        $this->appSettings = $appSettings;
    }

    public function dashboardAction(): ViewModel
    {
        return $this->view;
    }
}
