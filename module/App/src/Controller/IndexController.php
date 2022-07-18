<?php

/**
 * @codingStandardsIgnoreStart
 * @method \App\Controller\Plugin\Email email()
 * @codingStandardsIgnoreEnd
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Form\ContactForm;
use Laminas\Form\FormElementManager;
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
        $formManager = $this->service()->get(FormElementManager::class);
        $form        = $formManager->get(ContactForm::class);
        $appSettings = $this->service('config')['app_settings'];
        if ($this->request->isPost()) {
            $validationGroup = ['fullName', 'email', 'message'];
            if ($appSettings['security']['enable_captcha']) {
                $validationGroup[] = 'captcha';
            }
            $form->setValidationGroup($validationGroup);
            $form->setData($this->request->getPost()->toArray());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->email()->contactUsMessage($data['email'], $data['fullName'], $data['message']);
                $this->flashMessenger()->addSuccessMessage('Thank you for contacting us, your message was sent');
                return $this->redirect()->toRoute('home');
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
