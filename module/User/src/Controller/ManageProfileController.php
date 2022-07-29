<?php

declare(strict_types=1);

namespace User\Controller;

use App\Form\FormInterface;
use App\Log\LogEvent;
use App\Log\LoggerAwareInterface;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Form\FormElementManager;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Acl\CheckActionAccessTrait;
use User\Acl\ResourceAwareTrait;
use User\Form\ProfileForm;
use User\Service\UserService;

use function array_merge;
use function array_merge_recursive;
use function sprintf;

final class ManageProfileController extends AbstractActionController implements ResourceInterface, LoggerAwareInterface
{
    use CheckActionAccessTrait;
    use ResourceAwareTrait;
    use TranslatorAwareTrait;

    /** @var FormElementManager $formManager */
    protected $formManager;
    /** @var string $resourceId */
    protected $resourceId = 'profile';
    /** @var Translator $translator */
    protected $translator;
    /** @var UserService $userService */
    protected $userService;
    public function __construct(
        FormElementManager $formManager,
        Translator $translator,
        UserService $userService,
        array $config
    ) {
        $this->formManager = $formManager;
        $this->translator  = $translator;
        $this->userService = $userService;
    }

    public function editAddressAction()
    {

    }

    public function editSocialMediaAction(): ?ModelInterface
    {
        $htmlModel = new ViewModel();
        $htmlModel->setTerminal(true);
        $jsonModel = new JsonModel();
        $viewData  = [
            'message'       => 'success',
            'displayAction' => 'Editing Social Media',
            'form'          => '',
            'status'        => 'inprogress',
        ];
        if (! $this->isAllowed($this, 'edit')) { // if your not allowed were gonna tell ya you are not allowed
            $viewData['message']         = sprintf(
                $this->getTranslator()->translate('forbidden_known_action_403'),
                $this->getTranslator()->translate($this->params()->fromRoute('action'))
            );
            $veiewData['isJsonResponse'] = true;
            $jsonModel->setVariables($viewData);
            return $jsonModel;
        }
        // if we are here we need a form
        $form = $this->formManager->build(ProfileForm::class, ['action' => $this->params()->fromRoute('action')]);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('user/manage-profile', ['action' => 'edit-social-media'])
        );
        $userService = $this->identity()->getIdentity();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $formData            = $form->getData()['social-media']; // stopped working here
                $result              = $userService->save($formData, $formData['id']);
                $viewData['status']  = 'success';
                $viewData['message'] = $this->getTranslator()->translate('profile_social_update_success');
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

    public function editProfileAction(): mixed
    {
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
        $this->view->setVariable('form', $form);
        return $this->view;
    }
}
