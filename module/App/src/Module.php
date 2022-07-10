<?php

declare(strict_types=1);

namespace App;

use App\Listener\AdminListener;
use App\Listener\LayoutVariablesListener;
use App\Listener\ThemeLoader;
use App\Model\Theme;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Http\PhpEnvironment\Request as PhpRequest;
use Laminas\I18n\ConfigProvider;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Laminas\Session\SessionManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplateMapResolver;
use Laminas\View\Resolver\TemplatePathStack;
use Psr\Log\LoggerInterface;

use function date_default_timezone_set;
use function explode;
use function strpos;
use function substr;

final class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app          = $e->getApplication();
        $eventManager = $app->getEventManager();
        $sm           = $app->getServiceManager();
        $config       = $sm->get('config')['app_settings'];
        date_default_timezone_set($config['server']['time_zone']);
        GlobalAdapterFeature::setStaticAdapter($sm->get(AdapterInterface::class));
        $this->boostrapSessions($e);
        //$this->bootstrapLogging($e);
        // TODO: add theme loading based on user preference
        $themeLoader = new ThemeLoader($sm->get(Theme::class), $sm->get(TemplatePathStack::class));
        $themeLoader->attach($eventManager);
        $layoutVariables = new LayoutVariablesListener($sm->get('config')['app_settings']);
        $layoutVariables->attach($eventManager);
        $adminListener = new AdminListener($sm->get(TemplateMapResolver::class));
        $adminListener->attach($eventManager);
    }

    public function boostrapSessions(MvcEvent $e): void
    {
        $sm     = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        // db options
        $dbOptions      = [
            'idColumn'       => 'id',
            'nameColumn'     => 'name',
            'modifiedColumn' => 'modified',
            'lifetimeColumn' => 'lifetime',
            'dataColumn'     => 'data',
        ];
        $sessionManager = $sm->get(SessionManager::class);
        $saveHandler    = new DbTableGateway(
            new TableGateway(
                $config['db']['sessions_table_name'],
                $sm->get(AdapterInterface::class)
            ),
            new DbTableGatewayOptions($dbOptions)
        );
        $sessionManager->setSaveHandler($saveHandler);
        $container          = $sm->get(Container::class);
        $phpRequest         = $sm->get(PhpRequest::class);
        $container->prevUrl = $phpRequest->getServer()->get('HTTP_REFERER');
    }

    public function boostrapTranslation(MvcEvent $e): void
    {
        // get an instance of the service manager
        $sm       = $e->getApplication()->getServiceManager();
        $settings = $sm->get('config')['app_settings'];
        if ($settings['server']['enable_translation']) {
            $request = $sm->get('request');
            // get the laguages sent by the client
            $string = $request->getServer('HTTP_ACCEPT_LANGUAGE');
            // this should be delimeter for the first two prefrences set in the browser
            $needle = ';';
            // find its position
            $position = strpos($string, $needle);
            // return everything before the needle
            $substring = substr($string, 0, $position);
            // get an array of locales with the primary at offest 0
            $locales = explode(',', $substring);
            /**
             * @var $translator \Laminas\I18n\Translator\Translator
            */
            $translator = $sm->get('MvcTranslator');
            // set the primary locale as requested by the client
            $translator->setLocale($locales[0]);
            // set option two as the fallback
            $translator->setFallbackLocale([$locales[1]]);
            /**
             * @var $renderer \Laminas\View\Renderer\PhpRenderer
             */
            $renderer = $sm->get(PhpRenderer::class);
            // attach the Il8n standard helpers for translation
            $renderer->getHelperPluginManager()->configure((new ConfigProvider())->getViewHelperConfig());
        }
    }
}
