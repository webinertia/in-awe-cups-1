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
use User\Db\UserGateway;

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
        ?array $config = null,
        ?Logger $logger = null,
        ?UserGateway $userGateway = null
    ) {
        $this->appSettings = new Config($config['app_settings']);
        $this->config      = $config;
        $this->logger      = $logger;
        $this->view        = new ViewModel();
        $this->basePath    = dirname(__DIR__, 4);
        $this->usrModel    = $userGateway;

        $this->view->setVariables([
            'appSettings' => $this->appSettings,
            'resourceId'  => null,
        ]);
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }
}
