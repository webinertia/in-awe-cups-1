<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Settings;
use App\Service\Email;
use Laminas\Authentication\AuthenticationService;
use Laminas\Config\Config;
use Laminas\Form\FormElementManager;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use User\Model\Users as User;
use User\Permissions\PermissionsManager;
use Webinertia\ModelManager\ModelManager;

use function dirname;

abstract class AbstractAppController extends AbstractActionController
{
    /** @var PermissionsManager $acl */
        public $acl;

    /** @var Config $appSettings */
    public $appSettings;

    /** @var AuthenticationService $authService */
    public $authService;

    /** @var string $baseUrl */
    public $baseUrl;

    /** @var string $basePath */
    public $basePath;

    /** @var ContainerInterface $container */
    private $container;

    /** @var Email $emailService */
    protected $emailService;

    /** @var FormElementManager $formManager */
    protected $formManager;

    /** @var ModelManager $modelManager */
    protected $modelManager;

    /** @var string $referringUrl */
    public $referringUrl;

    /**
     * Holds the currently executing users info
     *
     * @var User\Model\Users $user
     * */
    protected $user;

    /**
     * Shared instance
     *
     * @var User\Model\Users $usrModel
     * */
    protected $usrModel;

    /** @var Logger $logger */
    protected $logger;

    /** @var ViewModel $view */
    protected $view;

    /** @var array $config */
    protected $config;

    /** @return void */
    public function __construct(
        ?ContainerInterface $container = null,
        ?Request $request = null,
        ?AuthenticationService $authService = null,
        ?Config $config = null,
        ?FormElementManager $formManager = null,
        ?Logger $logger = null,
        ?ModelManager $modelManager = null,
        ?Settings $appSettings = null,
        ?User $user = null,
        ?Email $emailService = null
    ) {
        $this->appSettings  = $appSettings;
        $this->authService  = $authService;
        $this->config       = $config;
        $this->emailService = $emailService;
        $this->formManager  = $formManager;
        $this->logger       = $logger;
        $this->modelManager = $modelManager;
        $this->user         = $user;
        $this->view         = new ViewModel();
        $this->baseUrl      = $request->getBasePath();
        $this->basePath     = dirname(__DIR__, 4);
        $this->usrModel     = $this->modelManager->get(User::class);

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
            'auth'        => $this->authService,
        ]);
        $this->init($container);
    }

    /**
     * @return AbstractAppController
     */
    public function init(ContainerInterface $container): self
    {
        $this->sessionContainer = $container->get(Container::class);
        $this->referringUrl     = $this->sessionContainer->prevUrl;
        return $this;
    }
}
