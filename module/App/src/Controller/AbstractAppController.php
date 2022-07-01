<?php

declare(strict_types=1);

namespace App\Controller;

use Laminas\Config\Config;
use Laminas\Log\LoggerAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Model\ViewModel;
use User\Db\UserGateway;

use function dirname;

abstract class AbstractAppController extends AbstractActionController implements ResourceInterface
{
    use LoggerAwareTrait;

    /** @var Config $appSettings */
    public $appSettings;

    /** @var string $baseUrl */
    public $baseUrl;

    /** @var string $basePath */
    public $basePath;

    /** @var string $referringUrl */
    public $referringUrl;

    /** @var string $resourceId */
    protected $resourceId;

    /**
     * Shared instance
     *
     * @var UserGateway
     * */
    protected $usrGateway;

    /** @var Logger $logger */
    protected $logger;

    /** @var ViewModel $view */
    protected $view;

    /** @var array $config */
    protected $config;

    /** @return void */
    public function __construct(
        ?array $config = null,
        ?UserGateway $userGateway = null
    ) {
        $this->appSettings = new Config($config['app_settings']);
        $this->config      = $config;
        $this->view        = new ViewModel();
        $this->basePath    = dirname(__DIR__, 4);
        $this->usrGateway  = $userGateway;

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
