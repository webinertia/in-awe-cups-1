<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Settings;
use Laminas\Config\Config;
use Laminas\Form\FormElementManager;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use User\Model\Users as User;
use Webinertia\ModelManager\ModelManager;

use function dirname;

abstract class AbstractAppController extends AbstractActionController implements ResourceInterface
{
    /** @var Config $appSettings */
    public $appSettings;

    /** @var string $baseUrl */
    public $baseUrl;

    /** @var string $basePath */
    public $basePath;

    /** @var ContainerInterface $container */
    private $container;

    /** @var FormElementManager $formManager */
    protected $formManager;

    /** @var ModelManager $modelManager */
    protected $modelManager;

    /** @var string $referringUrl */
    public $referringUrl;

    /** @var string $resourceId */
    protected $resourceId;

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
        ?Config $config = null,
        ?FormElementManager $formManager = null,
        ?Logger $logger = null,
        ?ModelManager $modelManager = null,
        ?Settings $appSettings = null
    ) {
        $this->appSettings  = $appSettings;
        $this->config       = $config;
        $this->formManager  = $formManager;
        $this->logger       = $logger;
        $this->modelManager = $modelManager;
        $this->view         = new ViewModel();
        $this->baseUrl      = $request->getBasePath();
        $this->basePath     = dirname(__DIR__, 4);
        $this->usrModel     = $this->modelManager->get(User::class);

        $this->view->setVariables([
            'appSettings' => $this->appSettings,
            'resourceId'  => null,
        ]);
        $this->init($container);
    }

    public function init(ContainerInterface $container): self
    {
        $this->sessionContainer = $container->get(Container::class);
        $this->referringUrl     = $this->sessionContainer->prevUrl;
        return $this;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }
}
