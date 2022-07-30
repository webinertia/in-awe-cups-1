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
use App\Log\LogEvent;
use ContentManager\Model\Page;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;

use function sprintf;

final class IndexController extends AbstractAppController
{
    /** @var ContactForm $form */
    protected $form;
    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var Page $page */
    protected $page;

    public function __construct(
        FormElementManager $formElementManager,
        Page $page,
        array $config,
    ) {
        $this->formManager = $formElementManager;
        $this->page        = $page;
        $this->appSettings = $config['app_settings'];
        $this->view        = new ViewModel();
        $this->view->setVariables([
            'appSettings' => $this->appSettings,
        ]);
    }

    public function indexAction(): mixed
    {
        $homePage = $this->page->getLandingPage();
        $this->view->setVariables([
            'page' => $homePage,
        ]);
        return $this->view;
    }

    public function contactAction(): mixed
    {
       // $formManager = $this->getService(FormElementManager::class);
        $form        = $this->formManager->get(ContactForm::class);
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
                    $this->getEventManager()->trigger(
                        LogEvent::NOTICE,
                        sprintf(
                            $this->getTranslator()->translate('log_contact_email_failure'),
                            $data['email'],
                            $data['firstName'] . ' ' . $data['lastName']
                        )
                    );
                    $this->flashMessenger()->addErrorMessage(
                        $this->getTranslator()->translate('contact_email_failure')
                    );
                }
                $this->getEventManager()->trigger(
                    LogEvent::INFO,
                    sprintf(
                        $this->getTranslator()->translate('log_contact_email_success'),
                        $data['email'],
                        $data['firstName'] . ' ' . $data['lastName']
                    )
                );
                // stopped work here
                $this->getEventManager()->trigger(
                    LogEvent::INFO,
                    sprintf(
                        $this->getTranslator()->translate('log_contact_email_success'),
                        $data['firstName'] . ' ' . $data['lastName'],
                        $data['email']
                    )
                );
                $this->flashMessenger()->addSuccessMessage(
                    $this->getTranslator()->translate('contact_email_success')
                );
                return $this->redirect()->toRoute('home');
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
