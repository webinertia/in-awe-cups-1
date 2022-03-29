<?php

declare(strict_types=1);

namespace User\Controller;

use Application\Controller\AbstractController;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use RuntimeException;
use User\Form\EditProfileForm;
use User\Model\Users;

use function array_merge_recursive;
use function substr;

class ProfileController extends AbstractController
{
    protected $usrModel;
    public function __construct(Users $usrModel)
    {
        $this->usrModel = $usrModel;
    }

    public function _init()
    {
        if (! $this->authService->hasIdentity()) {
            $this->redirect()->toRoute('user/account', ['action' => 'login']);
        }
    }

    public function viewAction()
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

    public function editProfileAction()
    {
        // Create the form object
        $form     = new EditProfileForm();
        $userName = $this->params()->fromRoute('userName');
        $user     = $this->table->fetchByColumn('userName', $this->params()->fromRoute('userName'));
        $profile  = $this->profileTable->fetchByColumn('userId', $user->id);
        if (! $this->request->isPost()) {
            return [
                'form' => $form,
            ];
        }
        // is this post?
        if ($this->request->isPost()) {
// We must merge the $_POST and $_FILES because Laminas\Http\Request remaps the $_FILES array
            $merged = array_merge_recursive($this->request->getPost()->toArray(), $this->request->getFiles()->toArray());
            unset($merged['submit']);
/**
             * setting this data should hydrate the bound $profile rowgateway object
             */
            $form->setData($merged);
            if ($form->isValid()) {
                $profile = $form->getData();
            // we should get a hydated rowgateway object here
                //var_dump($profile);
                // we will need this to rename and move the uploaded file
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
                $baseName = $baseNameFilter->filter($filtered['tmp_name']);
            //var_dump($baseName);
                $profile->profileImage = $baseName;
                try {
        //$profile true
                    $result = $this->profileTable->update($profile->toArray(), ['userId' => $profile->userId]);
                } catch (RuntimeException $e) {
                    echo $e->getMessage();
                }
                // $profile->populate($merged, true);
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
