<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\AdminControllerInterface;
use App\Form\SettingsForm;
use App\Form\ThemeSettingsForm;
use App\Model\Theme;
use Laminas\Config\Writer\PhpArray as ConfigWriter;
use Laminas\Form\FormElementManager;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Controller\WidgetController;

final class AdminController extends AbstractAppController implements AdminControllerInterface
{
    /** @var string $resourceId */
    protected $resourceId = 'admin';

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

    public function manageThemesAction(): ViewModel
    {
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        $themes  = $this->getService(Theme::class);
        $headers = $this->response->getHeaders();
        if (! $this->isAllowed($themes)) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $form = $this->getService(FormElementManager::class)->get(ThemeSettingsForm::class);
        $form->setAttribute('action', $this->url()->fromRoute('admin/themes/manage'));
        if (! $this->request->isPost()) {
            $form->setData($themes->getConfig());
        }
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $writer = $this->getService(ConfigWriter::class)->get();
                    $writer->setUseBracketArraySyntax(true);
                    $writer->toFile($this->getService(Theme::class)->get()->getConfigPath(), $data);
                    $headers->addHeaderLine('Content-Type', 'application/json');
                    $this->view->setVariables(['success' => true, 'message' => ['message' => 'Settings Saved']]);
                } catch (RuntimeException $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function manageSettingsAction(): ViewModel
    {
        $this->resourceId = 'settings';
        $headers          = $this->response->getHeaders();
        if ($this->request->isXmlHttpRequest()) {
            $this->view->setTerminal(true);
        }
        if (! $this->isAllowed($this)) {
            $headers->addHeaderLine('Content-Type', 'application/json');
            $this->response->setStatusCode(403);
            $this->view->setVariables(['error' => true, 'message' => ['message' => 'Access denied']]);
        }
        $form = $this->getService(FormElementManager::class)->get(SettingsForm::class);
        $form->setAttribute(
            'action',
            $this->url()->fromRoute('admin/settings/manage')
        );
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $writer = new ConfigWriter();
                $writer->setUseBracketArraySyntax(true);
                try {
                    $writer->toFile($this->basePath . '/config/autoload/appsettings.local.php', $form->getData());
                } catch (RuntimeException $e) {
                    $this->critical($e->getMessage());
                }
                $headers->addHeaderLine('Content-Type', 'application/json');
                $this->view->setVariables(['success' => true, 'message' => ['message' => 'Settings saved']]);
            }
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function addsettingAction(): mixed
    {
        $this->resourceId = 'settings';

        if (! $this->acl()->isAllowed($this->identity()->getIdentity(), $this, 'create')) {
            $this->flashMessenger()->addWarningMessage(
                'Access Denied, your attempt to access this area as been logged'
            );
            $this->response->setStatusCode(403);
        }
        $this->view->setVariable('resourceId', $this->resourceId);
        return $this->view;
    }
}
