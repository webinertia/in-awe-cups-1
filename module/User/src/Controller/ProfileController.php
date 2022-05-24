<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\ProfileForm;
use User\Model\Users;

use function array_merge_recursive;
use function substr;

class ProfileController extends AbstractController
{
    /** @var Users $usrModel */
    protected $usrModel;
    /** @var ProfileForm $form */
    /**
     * @return void
     */
    public function __construct(Users $usrModel, ProfileForm $profileForm)
    {
        $this->usrModel = $usrModel;
        $this->form     = $profileForm;
    }

    public function init(): self
    {
        if (! $this->authService->hasIdentity()) {
            $this->redirect()->toRoute('user/account', ['action' => 'login']);
        }
        return $this;
    }

    public function viewAction(): ViewModel
    {
        try {
            $userName              = $this->params()->fromRoute('userName');
            $requestedUser         = $this->usrModel->fetchByColumn('userName', ! empty($userName) ? $userName : $this->user->userName);
            $profileData           = $this->usrModel->fetchByColumn('userName', $requestedUser->userName);
            $profileData->userName = $requestedUser->userName;
            $previous              = substr($this->referringUrl, -5);
            if ($previous === 'login') {
                $this->logger->info('User ' . $this->user->userName . ' logged in.', $this->user->getLogData());
            }
            $this->view->setVariable('data', $profileData);
            return $this->view;
        } catch (RuntimeException $e) {
        //$this->logger->err($e->getMessage());
        }
    }

    public function editProfileAction(): mixed
    {
        $userName = $this->params()->fromRoute('userName');
        $user     = $this->usrModel->fetchByColumn('userName', $this->params()->fromRoute('userName'));
        if (! $this->request->isPost()) {
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
            /**
             * setting this data should hydrate the bound $profile rowgateway object
             */
            $this->form->setData($merged);
            if ($this->form->isValid()) {
                $profile    = $this->form->getData();
                $fileFilter = new RenameUpload();
                // set it to randomize the file name
                $fileFilter->setRandomize(true);
                // notice this sets the path for directory and the base file name used for all profile Images
                $fileFilter->setTarget($this->basePath . '/public/modules/user/profile/profileImages/profileImage');
                // maintain the original file extension
                $fileFilter->setUseUploadExtension(true);
                // perform the move and rename on the file
                $filtered       = $fileFilter->filter($profile->profileImage);
                $baseNameFilter = new BaseName();
                // grab just the file name so it can be stored in the profile table
                $baseName              = $baseNameFilter->filter($filtered['tmp_name']);
                $profile->profileImage = $baseName;
                try {
                    $result = $this->profileTable->update($profile->toArray(), ['userId' => $profile->userId]);
                } catch (RuntimeException $e) {
                    echo $e->getMessage();
                }
                // $profile->populate($merged, true);
            }
        }
        $this->view->setVariable('form', $this->form);
        return $this->view;
    }
}
