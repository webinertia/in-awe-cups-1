<?php

declare(strict_types=1);

namespace App;

use App\Listener\AdminListener;
use App\Listener\LayoutVariablesListener;
use App\Listener\ThemeLoader;
use App\Log\LogListener;
use App\Model\Theme;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\I18n\ConfigProvider;
use Laminas\I18n\Translator\Translator as TranslatorTranslator;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\View\Resolver\TemplatePathStack;
use Laminas\View\Strategy\JsonStrategy;
use Laminas\View\View;
use Locale;
use Psr\Log\LoggerInterface;

use function date_default_timezone_set;

final class Module
{
    /** @var ServiceLocatorInterface $sm */
    protected $sm;
    /** @var array<string, mixed> $config */
    protected $config;

    /** @return array<string, mixed> */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function init(ModuleManager $modulemanager): void
    {
        $events = $modulemanager->getEventManager();
        $events->attach(ModuleEvent::EVENT_MERGE_CONFIG, [$this, 'onMergeConfig']);
    }

    public function onMergeConfig(ModuleEvent $event): void
    {
        $configListener = $event->getConfigListener();
        $config         = $configListener->getMergedConfig(false);
        if (
            isset($config['session_config']['cookie_secure']) &&
            isset($GLOBALS['_SERVER']['REQUEST_SCHEME']) &&
            ! $config['session_config']['cookie_secure'] &&
            $GLOBALS['_SERVER']['REQUEST_SCHEME'] === 'https'
        ) {
            $config['session_config']['cookie_secure']   = true;
            $config['session_config']['cookie_samesite'] = 'None';
        }
    }

    public function onBootstrap(MvcEvent $e): void
    {
        $app          = $e->getApplication();
        $eventManager = $app->getEventManager();
        $this->sm     = $app->getServiceManager();
        $this->config = $this->sm->get('config');
        date_default_timezone_set($this->config['app_settings']['server']['time_zone']);
        GlobalAdapterFeature::setStaticAdapter($this->sm->get(AdapterInterface::class));
        $psrLogAdapter = $this->sm->get(LoggerInterface::class);
        $logListener   = new LogListener($psrLogAdapter);
        $logListener->attach($eventManager);
        if ($this->config['app_settings']['server']['log_errors'] && $this->sm->has(LoggerInterface::class)) {
            $log = $this->sm->get(LoggerInterface::class)->getLogger();
            $log::registerErrorHandler($log, true);
        }
        if ($this->config['app_settings']['server']['enable_translation']) {
            $this->boostrapTranslation($e);
        }
        $themeLoader = new ThemeLoader($this->sm->get(Theme::class), $this->sm->get(TemplatePathStack::class));
        $themeLoader->attach($eventManager);
        $layoutVariables = new LayoutVariablesListener($this->config['app_settings']);
        $layoutVariables->attach($eventManager);
        $adminListener = new AdminListener(
            $psrLogAdapter,
            $this->sm->get(TemplateMapResolver::class),
            $this->sm->get(TranslatorTranslator::class)
        );
        $adminListener->attach($eventManager);
        // attach the jsonsrategy to the event manager
        $eventManager->attach(MvcEvent::EVENT_RENDER, [$this, 'registerJsonStrategy'], 100);
    }

    public function registerJsonStrategy(MvcEvent $e)
    {
        $app          = $e->getApplication();
        $container    = $app->getServicemanager();
        $view         = $container->get(View::class);
        $jsonStrategy = $container->get(JsonStrategy::class);
        $jsonStrategy->attach($view->getEventManager(), 100);
    }

    public function boostrapTranslation(MvcEvent $e): void
    {
        // get an instance of the service manager
        $request = $this->sm->get(Request::class);
        // get the laguages sent by the client
        $locale     = Locale::acceptFromHttp($request->getServer('HTTP_ACCEPT_LANGUAGE'));
        $translator = $this->sm->get(Translator::class);
        // set the primary locale as requested by the client
        if ($locale !== null) {
            $translator->setLocale($locale);
            // set option two as the fallback
            $translator->setFallbackLocale('en_US');
        }
        $renderer = $this->sm->get(PhpRenderer::class);
        // attach the Il8n standard helpers for translation
        $renderer->getHelperPluginManager()->configure((new ConfigProvider())->getViewHelperConfig());
    }
}
