<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;
use User\Form\UserForm;
use Webinertia\Utils\Debug;

use function array_merge_recursive;

final class TestController extends AbstractAppController
{
    /** @var UserForm $form */
    protected $form;

    public function indexAction(): ViewModel
    {
       // $this->response->setStatusCode(500);
        $ident      = $this->authService->getIdentity();
        $this->form = $this->formManager->get(UserForm::class);
        if ($this->request->isPost()) {
            $this->form->setData($this->request->getPost()->toArray());
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $this->usrModel->exchangeArray(array_merge_recursive($data['acct-data'], $data['profile-data']));
            }
        }
        $this->form->addSubmit();
        $this->view->setVariable('form', $this->form);
        $user = $this->loadUser();
        $this->email()->sendMessage('jsmith@webinertia.net', 'welcome', 'ateststring');
        return $this->view;
    }
}
