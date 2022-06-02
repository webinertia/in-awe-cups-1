<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Settings;
use Laminas\Authentication\AuthenticationService;
use Laminas\Config\Config;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Exception\DomainException;
use Laminas\Mvc\Exception\RuntimeException;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Model\Users as User;
use User\Permissions\PermissionsManager;
use Webinertia\ModelManager\ModelManager;

use function dirname;

abstract class AbstractController extends AbstractActionController
{

    public $fm;

    /** @var ServiceManager $sm */
    public $sm;

    /** @var ModelManager $modelManager */
    protected $modelManager;

    /** @var string $baseUrl */
    public $baseUrl;

    /** @var string $basePath */
    public $basePath;

    /** @var string $referringUrl */
    public $referringUrl;

    /** @var AuthenticationService $authService */
    public $authService;

    /** @var User\Model\User $user */
    public $user;

    /** @var Logger $logger */
    public $logger;

    /** @var ViewModel $view */
    public $view;

    /** @var PermissionsManager $acl */
    public $acl;

    /** @var array $config */
    public $config;

    /** @var Config $appSettings */
    public $appSettings;

    /** @var string $action */
    protected $action;
    //protected $sessionContainer;
    /**
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        // Get an instance of the Service Manager
        $this->sm           = $e->getApplication()->getServiceManager();
        $config             = $this->sm->get('config');
        $this->modelManager = $this->sm->get(ModelManager::class);
        $this->user         = $this->modelManager->build(User::class);
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
        $this->appSettings = $this->modelManager->get(Settings::class);
        // This may be removed in next branch
        $pluginManager = $this->sm->get('ControllerPluginManager');
        $this->config  = $this->sm->get('config');
        // An instance of the Acl Service
        $this->acl  = $this->sm->get(PermissionsManager::class);
        $this->view = new ViewModel();
        // Is the User Authenticated?
        switch ($this->authService->hasIdentity()) {
            case true:
                $this->user = $this->user->fetchColumns(
                    'userName',
                    $this->authService->getIdentity(),
                    $this->user->getContextColumns()
                );
                break;
            case false:
            default:
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
        // $inlineLogin   = new ViewModel(['form' => ($this->sm->get(FormElementManager::class))->get(LoginForm::class)]);
        // $inlineLogin->setTemplate('user/partials/inline-login');
        // $rootViewModel->addChild($inlineLogin, 'inlineLogin');
        $this->action = $this->params()->fromRoute('action');
        $this->layout()->setVariables([
            'user'        => $this->user,
            'acl'         => $this->acl,
            'appSettings' => $this->appSettings,
            'auth'        => $this->authService,
        ]);
        $this->init();
        return parent::onDispatch($e);
    }

    public function init(): self
    {
        return $this;
    }
}
