<?php

declare(strict_types=1);

namespace User\Controller;

use App\Controller\AbstractAppController;
use App\Form\FormInterface;
use App\Form\FormManagerAwareInterface;
use App\Form\FormManagerAwareTrait;
use App\Log\LogEvent;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Form\ProfileForm;

use function array_merge;
use function array_merge_recursive;
use function sprintf;

final class ManageProfileController extends AbstractAppController implements FormManagerAwareInterface
{
    use FormManagerAwareTrait;

    /** @var array<mixed> $config */
    protected $config;
    /** @var string $resourceId */
    protected $resourceId = 'profile';

    public function addressAction()
    {
    }

    public function socialMediaAction(): ?ModelInterface
    {
        //$this->response->setStatusCode(403);

        $htmlModel = new ViewModel();
        $htmlModel->setTerminal(true);
        $jsonModel = new JsonModel();
        $viewData  = [
            'message'       => '',
            'displayAction' => 'Editing Social Media',
            'success'       => false,
            'target'        => $this->params()->fromRoute('action'),
            'isJson'        => false,
            'status'        => 'inprogress',
            'formHasErrors' => false,
        ];
        $headers   = $this->request->getHeaders();
        $accept    = $headers->get('Accept');
        if ($accept->match('application/json')) {
            $viewData['isJson'] = true;
            //return $jsonModel;
        }
        if (! $this->isAllowed($this, 'edit')) { // if your not allowed were gonna tell ya you are not allowed
            $viewData['message'] = sprintf(
                $this->getTranslator()->translate('forbidden_known_action_403'),
                $this->getTranslator()->translate($this->params()->fromRoute('action'))
            );
            $jsonModel->setVariables($viewData);
            //return $jsonModel;
        }
        // insure we are using data from the requested user, if none is passed then we are editing our own
        $userService          = $this->userService->fetchByColumn(
            'userName',
            $this->params()->fromRoute('userName', $this->identity()->getIdentity()->userName)
        );
        $viewData['userName'] = $userService->userName;
        // if we are here we need a form
        $form = $this->formManager->build(ProfileForm::class, ['action' => $this->params()->fromRoute('action')]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('user/manage-profile', ['action' => 'social-media'])
        );
        if ($this->request->isPost()) { // this should be a json request and response
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData()['social-media']; // stopped working here
                $result   = $this->userService->save($formData, $formData['id']);
                if ($result) {
                    $viewData['success'] = true;
                    $viewData['status']  = 'complete';
                    $viewData['message'] = sprintf(
                        $this->getTranslator()->translate('profile_social_update_success'),
                        $this->identity()->getIdentity()->getFullName()
                    );
                } else {
                    $viewData['message'] = sprintf(
                        $this->getTranslator()->translate('profile_social_update_failure'),
                        $this->identity()->getIdentity()->getFullName()
                    );
                }
                $jsonModel->setVariables($viewData += $formData);
                return $jsonModel;
            }
        } else {
            $requestedUserName = $this->params()->fromRoute('userName', $userService->userName);
            $form->bind($userService);
            if ($userService->userName === $requestedUserName) {
                $formData = $userService->toArray();
            } else {
                $user     = $userService->fetchByColumn('userName', $requestedUserName);
                $formData = $user->toArray();
            }
            $form->setData(['social-media' => $formData]);
        }
        $viewData['form'] = $form;
        $htmlModel->setVariables($viewData);
        return $htmlModel;
    }

    public function profileAction(): mixed
    {
        $view = new ViewModel();
        $form = $this->formManager->build(
            ProfileForm::class,
            ['mode' => FormInterface::EDIT_MODE]
        );
        $user = $this->userService->fetchByColumn('userName', $this->params()->fromRoute('userName'));
        if (! $this->request->isPost()) {
            foreach ($form->getFieldsets() as $fieldset) {
                $fieldset->populateValues($user->toArray());
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
                $appSettings    = $this->getService('config')['app_settings'];
                $moduleSettings = $this->getService('config')['module_settings']['user'];
                $fileFilter->setTarget(
                    $appSettings['server']['upload_basepath'] . $moduleSettings['server']['profile_image_target_path']
                );
                // maintain the original file extension
                $fileFilter->setUseUploadExtension(true);
                // perform the move and rename on the file
                $filtered       = $fileFilter->filter($data['profile-data']['profileImage']);
                $baseNameFilter = new BaseName();
                // grab just the file name so it can be stored in the profile table
                $baseName                             = $baseNameFilter->filter($filtered['tmp_name']);
                $data['profile-data']['profileImage'] = $baseName;
                try {
                    $result = $this->userService->save(array_merge(
                        $data['acct-data'],
                        $data['profile-data'],
                        $data['role-data']
                    ));
                    if (! $result) {
                        throw new RuntimeException('log_known_user_profile_update_failure');
                    }
                } catch (RuntimeException $e) {
                    $this->getEventManager()->trigger(
                        LogEvent::NOTICE,
                        sprintf(
                            $this->getTranslator()->translate($e->getMessage()),
                            $user->getFullName()
                        )
                    );
                }
            }
        }
        $view->setVariable('form', $form);
        return $view;
    }

    public function ajaxTemplateAction(): ?ViewModel
    {
        $view = new ViewModel();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
            $view->setTemplate('user/manage-profile/partials/' . $this->params()->fromRoute('section'));
            $userName = $this->params()->fromRoute('userName');
            $data     = $this->userService->fetchColumns(
                'userName',
                $userName,
                $this->userService->getSectionColumns($this->params()->fromRoute('section'))
            );
            $view->setVariable('data', $data);
        }
        return $view;
    }
}
