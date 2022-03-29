<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Settings;
use Laminas\Authentication\AuthenticationService;
use Laminas\Config\Config;
use Laminas\Form\FormElementManager;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Permission\Acl;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;
use User\Form\LoginForm;
use User\Model\Users as User;

use function dirname;

abstract class AbstractController extends AbstractActionController
{
    /** @var $fm \Laminas\Mvc\Plugin\FlashMessenger */
    public $fm;
/** @var ServiceManager $sm */
    public $sm;
    public $baseUrl;
    public $basePath;
/** @var string $referringUrl */
    public $referringUrl;
/** @var $authService \Laminas\Authentication\AuthenticationService */
    public $authService;
/** @var User\Model\User $user */
    public $user;
/** @var Logger $logger */
    public $logger;
/** @var ViewModel $view */
    public $view;
/** @var Acl $acl */
    public $acl;
    public $authenticated = false;
/** @var $config array */
    public $config;
/** @var Config $appSettings */
    public $appSettings;
    protected $action;
    protected $sessionContainer;
    public function onDispatch(MvcEvent $e)
    {
        // Get an instance of the Service Manager
        $this->sm   = $e->getApplication()->getServiceManager();
        $config     = $this->sm->get('config');
        $this->user = $this->sm->build(User::class);
    // Request Object
        $request = $this->sm->get('Request');
    // The Referring Url for the current request ie the previous page
        $this->referringUrl = $request->getServer()->get('HTTP_REFERER');
    // The Logger Service
        $this->logger = $this->sm->get(Logger::class);
    // Not sure why we need this....
        $this->baseUrl  = $this->request->getBasePath();
        $this->basePath = dirname(__DIR__, 4);
    // The authentication Object
        $this->authService = $this->sm->get(AuthenticationService::class);
    // This removes the need for more than one db query to make settings available to Aurora
        $this->appSettings = $this->sm->get(Settings::class);
    // This may be removed in next branch
        $pluginManager = $this->sm->get('ControllerPluginManager');
        $this->config  = $this->sm->get('config');
    // An instance of the Acl Service
        $this->acl = $this->sm->get('Acl');
    //var_dump($this->baseDir);
        /**
         * @var $view \Laminas\View\Model\ViewModel
         */
        $this->view = new ViewModel();
        $check      = $this->authService->hasIdentity();
    //$this->authService->clearIdentity();
        // Is the User Authenticated?
        switch ($this->authService->hasIdentity()) {
            case true:
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $this->user = $this->user->fetchUserContext($this->authService->getIdentity());

                break;
            case false:
            default;
                $this->user->exchangeArray($this->user->fetchGuestContext());

                break;
        }
        $this->view->setVariables([
            'appSettings' => $this->appSettings,
            'user'        => $this->user,
            'acl'         => $this->acl,
            'auth'        => $this->authService,
        ]);
        $rootViewModel = $this->layout();
        $inlineLogin   = new ViewModel(['form' => ($this->sm->get(FormElementManager::class))->get(LoginForm::class)]);
        $inlineLogin->setTemplate('user/partials/inline-login');
        $rootViewModel->addChild($inlineLogin, 'inlineLogin');
    //$this->layout()->appSettings = $this->appSettings;
        $this->action = $this->params()->fromRoute('action');
        //$this->layout()->acl = $this->acl;
        $this->layout()->setVariables([
            'user'        => $this->user,
            'acl'         => $this->acl,
            'appSettings' => $this->appSettings,
            'auth'        => $this->authService,
        ]);
        $this->_init();
        return parent::onDispatch($e);
    }

    public function _init()
    {
        return $this;
    }
}
