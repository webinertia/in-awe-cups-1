<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
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
        if (! $this->identity()->hasIdentity()) {
            $this->redirect()->toRoute('user/login');
        }
        return parent::onDispatch($e);
    }

    public function viewAction(): ViewModel
    {
        try {
            $user     = $this->identity()->getIdentity();
            $userName = $this->params()->fromRoute('userName', $user->userName);
            if ($userName === $user->userName) {
                $profileData = $user;
            } else {
                $profileData = $this->usrModel->fetchByColumn('userName', $userName);
            }
            $this->view->setVariable('data', $profileData);
        } catch (RuntimeException $e) {
            $this->getLogger()->err($e->getMessage(), $this->identity()->getLogData());
            $this->view->setVariables(['message' => 'User not found', 'reason' => null]);
            $this->response->setStatusCode(404);
        }
        return $this->view;
    }

    public function editProfileAction(): mixed
    {
        $form = $this->service()->get(FormElementManager::class)->build(
            ProfileForm::class,
            ['mode' => FormInterface::EDIT_MODE]
        );
        $user = $this->usrModel->fetchByColumn('userName', $this->params()->fromRoute('userName'));
        if (! $this->request->isPost()) {
            foreach ($form->getFieldsets() as $fieldset) {
                $fieldset->populateValues($user->getArrayCopy());
            }
            return [
                'form' => $form,
            ];
        }
        // is this post?
        if ($this->request->isPost()) {
            $merged = array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );
            unset($merged['submit']);
            $form->setData($merged);
            if ($form->isValid()) {
                $data       = $form->getData();
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
                    $result = $this->usrModel->update(array_merge(
                        $data['acct-data'],
                        $data['profile-data'],
                        $data['role-data']
                    ));
                } catch (RuntimeException $e) {
                    $this->getLogger()->err($e->getMessage(), $this->identity()->getLogData());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
