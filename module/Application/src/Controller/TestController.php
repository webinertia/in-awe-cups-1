<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Controller\AbstractController;
use Application\Model\Settings;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\SessionManager;
use User\Form\UserForm;
use User\Model\Users;

use function array_merge_recursive;

class TestController extends AbstractController
{
    /** @var UserForm $form */
    protected $form;
/**
 * @return void
 * @throws NotFoundExceptionInterface
 * @throws ContainerExceptionInterface
 */
    public function __construct(ServiceLocatorInterface $sm)
    {
        $fieldsets            = [];
        $this->usrModel       = $sm->get(Users::class);
        $this->appSettings    = $sm->get(Settings::class);
        $fm                   = $sm->get(FormElementManager::class);
        $this->form           = $fm->get(UserForm::class);
        $this->config         = $sm->get('config');
        $this->sessionManager = $sm->get(SessionManager::class);
    }

    public function indexAction()
    {
        $ident = $this->authService->getIdentity();
        if ($this->request->isPost()) {
            $this->form->setData($this->request->getPost()->toArray());
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $this->usrModel->exchangeArray(array_merge_recursive($data['acct-data'], $data['profile-data']));
            }
        } else {
        }
        $this->form->addSubmit();
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }
}
