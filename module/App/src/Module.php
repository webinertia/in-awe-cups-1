<?php

declare(strict_types=1);

namespace App;

use App\Listener\AdminListener;
use App\Listener\LayoutVariablesListener;
use App\Listener\ThemeLoader;
use App\Model\Theme;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Http\PhpEnvironment\Request as PhpRequest;
use Laminas\I18n\ConfigProvider;
use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;
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

    public function onBootstrap(MvcEvent $e): void
    {
        $app          = $e->getApplication();
        $eventManager = $app->getEventManager();
        $this->sm     = $app->getServiceManager();
        $this->config = $this->sm->get('config');
        date_default_timezone_set($this->config['app_settings']['server']['time_zone']);
        GlobalAdapterFeature::setStaticAdapter($this->sm->get(AdapterInterface::class));
        $this->boostrapSessions($e);
        if ($this->config['app_settings']['server']['log_errors'] && $this->sm->has(LoggerInterface::class)) {
            $log = $this->sm->get(LoggerInterface::class)->getLogger();
            $log::registerErrorHandler($log, true);
        }
        $themeLoader = new ThemeLoader($this->sm->get(Theme::class), $this->sm->get(TemplatePathStack::class));
        $themeLoader->attach($eventManager);
        $layoutVariables = new LayoutVariablesListener($this->config['app_settings']);
        $layoutVariables->attach($eventManager);
        $adminListener = new AdminListener($this->sm->get(TemplateMapResolver::class));
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

    public function boostrapSessions(MvcEvent $e): void
    {
        $phpRequest = $this->sm->get(PhpRequest::class);
        if (
            $phpRequest->getServer()->get('REQUEST_SCHEME') === 'https' &&
            ! $this->config['session_config']['cookie_secure']
        ) {
            $this->config['session_config']['cookie_secure'] = true;
        }
        $container          = $this->sm->get(Container::class);
        $container->prevUrl = $phpRequest->getServer()->get('HTTP_REFERER');
    }

    public function boostrapTranslation(MvcEvent $e): void
    {
        // get an instance of the service manager
        if ($this->config['app_settings']['server']['enable_translation']) {
            $request = $this->sm->get('request');
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
}
