<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Form\ContactForm;
use App\Service\Email;
use Laminas\Http\Exception\InvalidArgumentException;
use Laminas\View\Model\ViewModel;

final class IndexController extends AbstractAppController
{
    /** @var ContactForm $form */
    protected $form;

    public function indexAction(): ViewModel
    {
        return $this->view;
    }

    public function contactAction(): mixed
    {
        $this->form = $this->formManager->get(ContactForm::class);
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
}
