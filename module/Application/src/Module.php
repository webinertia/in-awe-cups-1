<?php

declare(strict_types=1);

namespace Application;

use Application\Model\Settings;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\I18n\ConfigProvider;
use Laminas\Log\Filter\Priority;
use Laminas\Log\Formatter\Db as DbFormatter;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Db as Dbwriter;
use Laminas\Log\Writer\FirePhp;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Laminas\Session\SessionManager;

use function date_default_timezone_set;
use function explode;
use function strpos;
use function substr;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap($e)
    {
        $sm     = $e->getApplication()->getServiceManager();
        $config = $sm->get(Settings::class);
        date_default_timezone_set($config->server->time_zone);
        GlobalAdapterFeature::setStaticAdapter($sm->get(AdapterInterface::class));
        $this->boostrapSessions($e);
        $this->bootstrapLogging($e);
    }

    public function boostrapSessions($e)
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
        $saveHandler    = new DbTableGateway(new TableGateway($config['db']['sessions_table_name'], $sm->get(AdapterInterface::class)), new DbTableGatewayOptions($dbOptions));
        $sessionManager->setSaveHandler($saveHandler);
    }

    // this needs moved to a listener
    public function boostrapTranslation($e)
    {
        // get an instance of the service manager
        $sm       = $e->getApplication()->getServiceManager();
        $settings = $sm->get(Settings::class);
        if ($settings->server_settings->enable_translation) {
        // var_dump($sm->get('router'));
                /**
                 * @var $request \Laminas\Http\PhpEnvironment\Request
                 */
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

    public function bootstrapLogging($e)
    {
        $sm       = $e->getapplication()->getServiceManager();
        $settings = $sm->get(Settings::class);
        $config   = $sm->get('config');
        $logger   = $sm->get(Logger::class);
//$writer = new Dbwriter(new Adapter($config['db']), 'log');
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
}
