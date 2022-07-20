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
use RuntimeException;

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
        $formManager = $this->getService(FormElementManager::class);
        $form        = $formManager->get(ContactForm::class);
        $appSettings = $this->getService('config')['app_settings'];
        if ($this->request->isPost()) {
            $validationGroup = ['fullName', 'email', 'message'];
            if ($appSettings['security']['enable_captcha']) {
                $validationGroup[] = 'captcha';
            }
            $form->setValidationGroup($validationGroup);
            $form->setData($this->request->getPost()->toArray());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $this->email()->contactUsMessage($data['email'], $data['fullName'], $data['message']);
                } catch (RuntimeException $e) {
                    $this->warning(
                        $e->getMessage(),
                        ['firstName' => $data['firstName'], 'lastName' => $data['lastName'], 'email' => $data['email']]
                    );
                    $this->flashMessenger()->addErrorMessage('Your message could not be sent');
                }
                $this->info(
                    'Guest with the name '
                    . $data['firstName']
                    . ' ' . $data['lastName']
                    . ' has sent a message using email: '
                    . $data['email'],
                    ['firstName' => $data['firstName'], 'lastName' => $data['lastName'], 'email' => $data['email']]
                );
                $this->flashMessenger()->addSuccessMessage('Thank you for contacting us, your message has been sent');
                return $this->redirect()->toRoute('home');
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
