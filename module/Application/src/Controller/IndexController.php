<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Form\ContactForm;
use Application\Service\Email;
use Laminas\Authentication\Storage\Session;
use Laminas\Session\SessionManager;

class IndexController extends AbstractController
{
    /** @var User\Form\ContactForm $form */
    protected $form;
/**
 * @return void
 */
    public function __construct(ContactForm $form)
    {
        $this->form = $form;
    }

    public function indexAction()
    {
        if ($this->authService->hasIdentity()) {
            $storage  = new Session(Session::NAMESPACE_DEFAULT, $this->authService->getIdentity(), $this->sm->get(SessionManager::class));
            $userName = $this->authService->getIdentity();
            $user     = $storage->getMember();
        }
        return $this->view;
    }

    public function contactAction()
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
        } else {
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }

    public function forbiddenAction()
    {
        $this->response->setStatusCode(403);
        return $this->view;
    }
}
