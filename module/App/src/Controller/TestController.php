<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController;
use App\Model\Settings;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use User\Form\UserForm;
use User\Model\Users;
use Webinertia\ModelManager\ModelManager;

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
        $modelManager         = $sm->get(ModelManager::class);
        $this->usrModel       = $modelManager->get(Users::class);
        $this->appSettings    = $modelManager->get(Settings::class);
        $fm                   = $sm->get(FormElementManager::class);
        $this->form           = $fm->get(UserForm::class);
        $this->config         = $sm->get('config');
        $this->sessionManager = $sm->get(SessionManager::class);
    }

    public function indexAction(): ViewModel
    {
        $ident = $this->authService->getIdentity();
        if ($this->request->isPost()) {
            $this->form->setData($this->request->getPost()->toArray());
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $this->usrModel->exchangeArray(array_merge_recursive($data['acct-data'], $data['profile-data']));
            }
        }
        $this->form->addSubmit();
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }
}
