<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\SettingsForm;
use Laminas\Config\Writer\PhpArray as ConfigWriter;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use User\Controller\WidgetController;

use function strtolower;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function indexAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $memberListWidget = $this->forward()->dispatch(
            WidgetController::class,
            [
                'action' => 'member-list',
                'group'  => 'admin',
            ]
        );
        $this->view->setVariables(['memberListWidget' => $memberListWidget, 'listType' => 'admin']);
        return $this->view;
    }

    public function manageSettingsAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $settings = $this->service()->get('config')['app_settings'];
        $form     = $this->service()->get(FormElementManager::class)->get(SettingsForm::class);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin/settings')
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data as $key => $value) {
                    $settings[$key] = $value;
                }
                $writer = new ConfigWriter();
                $writer->toFile($this->basePath . '/config/autoload/test.php', $form->getData());
                $headers = $this->response->getHeaders();
            }
        } else {
            $form->setData($settings);
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function addsettingAction(): mixed
    {
        $this->resourceId = 'settings';

        if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
            $this->flashMessenger()->addWarningMessage('Access Denied, your attempt to access this area as been logged');
            $this->response->setStatusCode('403');
        }
        $this->view->setVariable('resourceId', $this->resourceId);
        return $this->view;
    }
}
