<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController;
use App\Form\ContactForm;
use App\Service\Email;
use Laminas\Authentication\Storage\Session;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;

final class IndexController extends AbstractController
{
    /** @var ContactForm $form */
    protected $form;
    /** @return void */
    public function __construct(ContactForm $form)
    {
        $this->form = $form;
    }

    public function indexAction(): ViewModel
    {
        if ($this->authService->hasIdentity()) {
            $storage  = new Session(Session::NAMESPACE_DEFAULT, $this->authService->getIdentity(), $this->sm->get(SessionManager::class));
            $userName = $this->authService->getIdentity();
            $user     = $storage->getMember();
        }
        return $this->view;
    }

    public function contactAction(): mixed
    {
        //todo:: start with this form on the refactoring to fieldsets and delegators
        if ($this->request->isPost()) {
            $validationGroup = ['fullName', 'email', 'message'];
            if ($this->appSettings->security->enable_captcha) {
                $validationGroup[] = 'captcha';
            }
            $this->form->setValidationGroup($validationGroup);
            $this->form->setData($this->request->getPost()->toArray());
            if ($this->form->isValid()) {
                $data   = $this->form->getData();
                $mailer = $this->sm->get(Email::class);
                $mailer->contactUsMessage($data['email'], $data['fullName'], $data['message']);
                $this->flashMessenger()->addSuccessMessage('Thank you for contacting us, your message was sent');
                return $this->redirect()->toRoute('home');
            }
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function forbiddenAction(): ViewModel
    {
        $this->response->setStatusCode(403);
        return $this->view;
    }
}
