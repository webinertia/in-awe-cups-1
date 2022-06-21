<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use RuntimeException;
use User\Form\ProfileForm;
use User\Model\Users;

use function array_merge;
use function array_merge_recursive;

final class ProfileController extends AbstractAppController
{
    /** @var Users $usrModel */
    protected $usrModel;
    /** @var ProfileForm $form */
    protected $form;
    /**
     * @return mixed
     * @throws DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        if (! $this->authService->hasIdentity()) {
            $this->redirect()->toRoute('user/login');
        }
        return parent::onDispatch($e);
    }

    /** @param ContainerInterface $container */
    public function init($container): self
    {
        $this->authService      = $container->get(AuthenticationService::class);
        $this->form             = $this->formManager->get(ProfileForm::class);
        $this->sessionContainer = $container->get(Container::class);
        return $this;
    }

    public function viewAction(): ViewModel
    {
        try {
            $user     = $this->loadUser();
            $userName = $this->params()->fromRoute('userName', $user->userName);
            if ($userName === $user->userName) {
                $profileData = $user;
            } else {
                $profileData = $this->usrModel->fetchByColumn('userName', $userName);
            }
            $this->view->setVariable('data', $profileData);
            return $this->view;
        } catch (RuntimeException $e) {
        //$this->logger->err($e->getMessage());
        }
    }

    public function editProfileAction(): mixed
    {
        $this->form = $this->formManager->build(ProfileForm::class, ['mode' => FormInterface::EDIT_MODE]);
        $userName   = $this->params()->fromRoute('userName');
        $user       = $this->usrModel->fetchByColumn('userName', $this->params()->fromRoute('userName'));
        if (! $this->request->isPost()) {
            foreach ($this->form->getFieldsets() as $fieldset) {
                $fieldset->populateValues($user->getArrayCopy());
            }
            return [
                'form' => $this->form,
            ];
        }
        // is this post?
        if ($this->request->isPost()) {
            $merged = array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );
            unset($merged['submit']);
            $this->form->setData($merged);
            if ($this->form->isValid()) {
                $data       = $this->form->getData();
                $fileFilter = new RenameUpload();
                // set it to randomize the file name
                $fileFilter->setRandomize(true);
                // notice this sets the path for directory and the base file name used for all profile Images
                $fileFilter->setTarget($this->basePath . '/public/modules/user/profile/profileImages/profileImage');
                // maintain the original file extension
                $fileFilter->setUseUploadExtension(true);
                // perform the move and rename on the file
                $filtered       = $fileFilter->filter($data['profile-data']['profileImage']);
                $baseNameFilter = new BaseName();
                // grab just the file name so it can be stored in the profile table
                $baseName                             = $baseNameFilter->filter($filtered['tmp_name']);
                $data['profile-data']['profileImage'] = $baseName;
                try {
                    //$result = $user->update($profile->toArray(), ['userId' => $profile->userId]);
                    $this->usrModel->exchangeArray(
                        array_merge($data['acct-data'], $data['profile-data'], $data['role-data'])
                    );
                    $result = $this->usrModel->update($this->usrModel);
                } catch (RuntimeException $e) {
                    echo $e->getMessage();
                }
            }
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }
}
