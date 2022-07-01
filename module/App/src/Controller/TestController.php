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
       $currentUser = $this->identity()->getIdentity();
       $testUser = $this->usrGateway->fetchColumns('userName', 'jsmith');
       $testOwnerOwnerId = $testUser->getOwnerId();
           // echo $this->acl()->isAllowed($this->identity()->getIdentity(), $testUser, 'edit') ? 'allowed' : 'not allowed';
            Debug::dump(opcache_get_configuration());
        return $this->view;
    }
}
