<?php

declare(strict_types=1);

namespace Application;

use Application\Model\Settings;
use Application\Model\Theme;
use ContentManager\Model\Pages;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\I18n\ConfigProvider;
use Laminas\Log\Filter\Priority;
use Laminas\Log\Formatter\Db as DbFormatter;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Db as Dbwriter;
use Laminas\Log\Writer\FirePhp;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Laminas\Session\SessionManager;
use Laminas\View\Helper\Navigation;
use Webinertia\ModelManager\ModelManager;

use function date_default_timezone_set;
use function explode;
use function strpos;
use function substr;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $sm                 = $e->getApplication()->getServiceManager();
        $this->modelManager = $sm->get(ModelManager::class);
        $config             = $this->modelManager->get(Settings::class);
        date_default_timezone_set($config->server->time_zone);
        GlobalAdapterFeature::setStaticAdapter($sm->get(AdapterInterface::class));
        $this->boostrapSessions($e);
        $this->bootstrapLogging($e);
        $this->bootstrapNavigation($e);
        $this->bootstrapTheme($e);
    }

    public function boostrapSessions(MvcEvent $e): void
    {
        $sm        = $e->getApplication()->getServiceManager();
        $config    = $sm->get('Config');
        $dbOptions = [
            'idColumn'       => 'id',
            'nameColumn'     => 'name',
            'modifiedColumn' => 'modified',
            'lifetimeColumn' => 'lifetime',
            'dataColumn'     => 'data',
        ];
        /**
         * @var SessionManager $sessionManager
         */
        $sessionManager = $sm->get(SessionManager::class);
        $saveHandler    = new DbTableGateway(
            new TableGateway(
                $config['db']['sessions_table_name'],
                $sm->get(AdapterInterface::class)
            ),
            new DbTableGatewayOptions($dbOptions)
        );
        $sessionManager->setSaveHandler($saveHandler);
    }

    public function boostrapTranslation(MvcEvent $e): void
    {
        // get an instance of the service manager
        $sm       = $e->getApplication()->getServiceManager();
        $settings = $this->modelManager->get(Settings::class);
        if ($settings->server_settings->enable_translation) {
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
            $renderer = $sm->get('ViewRenderer');
        // attach the Il8n standard helpers for translation
            $renderer->getHelperPluginManager()->configure((new ConfigProvider())->getViewHelperConfig());
        }
    }

    public function bootstrapLogging(MvcEvent $e): void
    {
        $sm                = $e->getapplication()->getServiceManager();
        $settings          = $this->modelManager->get(Settings::class);
        $config            = $sm->get('config');
        $logger            = $sm->get(Logger::class);
        $writer            = new Dbwriter($sm->get(AdapterInterface::class), $config['db']['log_table_name']);
        $standardLogFilter = new Priority(Logger::DEBUG);
        $writer->addFilter($standardLogFilter);
        if ($settings->server->enable_firebug_debug) {
            $firePhpWriter = new FirePhp();
            $debugFilter   = new Priority(Logger::DEBUG);
            $firePhpWriter->addFilter($debugFilter);
            $writer->addFilter($debugFilter);
            $logger->addWriter($firePhpWriter);
        }

        $dbFormatter = new DbFormatter();
        $dbFormatter->setDateTimeFormat($settings->timeFormat);
        $writer->setFormatter($dbFormatter);
        $logger->addWriter($writer);
        if ($settings->enable_error_log) {
            Logger::registerErrorHandler($logger);
        }
    }

    public function bootstrapNavigation(MvcEvent $e)
    {
        $sm           = $e->getApplication()->getServiceManager();
        $vhm          = $sm->get('ViewRenderer')->getHelperPluginManager();
        $modelManager = $sm->get(ModelManager::class);
        $navigation   = $vhm->get(Navigation::class);
        $navContainer = $navigation('Laminas\Navigation\Default');
        $menu         = $modelManager->get(Pages::class)->fetchMenu();
        $navContainer->addPages($menu);
    }
    /** DO NOT MODIFY THIS METHOD */
    public function bootstrapTheme(MvcEvent $e)
    {
        $sm                = $e->getApplication()->getServiceManager();
        $theme             = $sm->get(ModelManager::class)->get(Theme::class);
        $templatePathStack = $sm->get('ViewTemplatePathStack');
        $templatePathStack->addPaths($theme->getThemePaths());
    }
}
