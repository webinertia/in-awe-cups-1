<?php

declare(strict_types=1);

namespace App;

use App\Log\LogListener;
use Laminas\I18n\ConfigProvider;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
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
        $psrLogAdapter = $this->sm->get(LoggerInterface::class);
        $logListener   = new LogListener($psrLogAdapter);
        $logListener->attach($eventManager);
        // is error logging enabled?
        if ($this->config['app_settings']['server']['log_errors'] && $this->sm->has(LoggerInterface::class)) {
            $log = $this->sm->get(LoggerInterface::class)->getLogger();
            $log::registerErrorHandler($log, true);
        }
        if ($this->config['app_settings']['server']['enable_translation']) {
            $this->boostrapTranslation($e);
        }
    }

    public function boostrapTranslation(MvcEvent $e): void
    {
        // get an instance of the Request object
        $request = $this->sm->get('Request');
        // what locale has the client set in their browser?
        $locale     = Locale::acceptFromHttp($request->getServer()->get('HTTP_ACCEPT_LANGUAGE', 'en_US'));
        $translator = $this->sm->get(Translator::class);
        // set the primary locale as requested by the client
        if ($locale !== null) {
            $translator->setLocale($locale);
            // set the fallback
            $translator->setFallbackLocale('en_US');
        }
        $renderer = $this->sm->get(PhpRenderer::class);
        /**
         * This allows view helpers to automatically translate things like
         * the Menu component if the labels have been translated.
         * Also allows for translating Form validator messages.
         **/
        $renderer->getHelperPluginManager()->configure((new ConfigProvider())->getViewHelperConfig());
    }
}
