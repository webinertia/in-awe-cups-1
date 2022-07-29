<?php

declare(strict_types=1);

namespace App\Log;

use App\Log\LogEvent;
use App\Log\LoggerAwareInterface;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\I18n\Translator;
use Psr\Log\LoggerInterface;

final class LogListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;
    use LoggerAwareTrait;

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /** @inheritDoc */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $sharedMananger    = $events->getSharedManager();
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::EMERGENCY,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ALERT,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::CRITICAL,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::ERROR,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::WARNING,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::NOTICE,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::INFO,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $sharedMananger->attach(
            LoggerAwareInterface::class,
            LogEvent::DEBUG,
            [$this, 'log'],
            $priority
        );
    }

    public function log(EventInterface $event)
    {
        $passContext = false;
        $name        = $event->getName();
        $logMessage  = $event->getTarget();
        $params      = $event->getParams();
        if ($params !== []) {
            $passContext = true;
        }
        if ($passContext) {
            unset($params['translate']);
            $this->logger->$name($logMessage, $params);
        } else {
            unset($params);
            $this->logger->$name($logMessage);
        }
       // $this->logger->$name($logMessage, $params);
    }
}
