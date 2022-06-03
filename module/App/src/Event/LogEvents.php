<?php

declare(strict_types=1);

namespace App\Event;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Log\Logger;
use Laminas\Log\Processor\ReferenceId;

final class LogEvents implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /** @var Logger */
    private $log;
    /** @var ReferenceId $proc */
    private $proc;

    public function __construct(Logger $log)
    {
        $this->log  = $log;
        $this->proc = new ReferenceId();
    }

    /** @param int $priority */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach('save', [$this, 'log']);
        $this->listeners[] = $events->attach('update', [$this, 'log']);
        $this->listeners[] = $events->attach('send', [$this, 'log']);
        $this->listeners[] = $events->attach('test', [$this, 'log']);
    }

    public function log(EventInterface $e)
    {
        $event  = $e->getName();
        $params = $e->getParams();
        if (! empty($params['userId'])) {
            $this->proc->setReferenceId($params['userId']);
            $this->log->addProcessor($this->proc);
        }
        switch ($event) {
            case 'send':
                $extra['extra']['userId'] = $params['userId'];
                //var_dump($extra);
                $this->log->log(Logger::INFO, $params);
                break;
            case 'save':
                $extra['extra']['userId'] = $params['userId'];
                //var_dump($extra);
                $this->log->log(Logger::INFO, $params);
                break;
            case 'update':
                $extra['extra']['userId'] = $params['userId'];
                $this->log->log(Logger::INFO, $params);
                break;
            default:
                break;
        }
       // $this->log->info(sprintf('%s: %s', $event, json_encode($params)));
    }
}
