<?php

declare(strict_types=1);

namespace App\Controller;

use App\Log\LoggerAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Model\ViewModel;
use User\Acl\CheckActionAccessTrait;
use User\Acl\ResourceAwareTrait;
use User\Db\UserGateway;

use function dirname;

/**
 * Abstract App Controller
 * Plugin and trait method signatures for static analysis
 * @codingStandardsIgnoreStart
 * @method \App\Controller\Plugin\Email email()
 * @method \App\Controller\Plugin\ServiceLocator getService(string $serviceName)
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 * @method \User\Controller\Plugin\Acl acl()
 * @method \User\Acl\CheckActionAccessTrait isAllowed(?ResourceInterface $resourceInterface = null)
 * @method \User\Controller\Plugin\Identity identity()
 * @method \User\Acl\CheckActionAccessTrait isAllowed(?ResourceInterface $resourceInterface = null)
 * @method \Laminas\Http\PhpEnvironment\Request getRequest()
 * @method \Laminas\Http\PhpEnvironment\Response getResponse()
 * @codingStandardsIgnoreEnd
 */

abstract class AbstractAppController extends AbstractActionController implements ResourceInterface, LoggerAwareInterface
{
    use CheckActionAccessTrait;
    use ResourceAwareTrait;
    use TranslatorAwareTrait;

    /** @var array<mixed> $appSettings */
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

    /** @var ViewModel $view */
    protected $view;

    /** @var array<string, mixed> $config */
    protected $config;

    /**
     * @return void
     * @param array<string, mixed> $config
     * */
    public function __construct(
        ?array $config = null,
        ?UserGateway $userGateway = null
    ) {
        $this->appSettings = $config['app_settings'];
        $this->config      = $config;
        $this->view        = new ViewModel();
        $this->basePath    = dirname(__DIR__, 4);
        $this->usrGateway  = $userGateway;

        $this->view->setVariables([
            'appSettings' => $this->appSettings,
            'resourceId'  => null,
        ]);
    }
}
