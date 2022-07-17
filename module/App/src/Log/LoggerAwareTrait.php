<?php

/**
 * Logger aware trait
 *
 * @method LoggerInterface emergency($message, array $context = array())
 * @method LoggerInterface alert($message, array $context = array())
 * @method LoggerInterface critical($message, array $context = array())
 * @method LoggerInterface error($message, array $context = array())
 * @method LoggerInterface warning($message, array $context = array())
 * @method LoggerInterface notice($message, array $context = array())
 * @method LoggerInterface info($message, array $context = array())
 * @method LoggerInterface debug($message, array $context = array())
 */

declare(strict_types=1);

namespace App\Log;

use Laminas\Log\LoggerAwareTrait as LmLoggerAwareTrait;
use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;
use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    use LmLoggerAwareTrait, PsrLoggerAwareTrait {
        PsrLoggerAwareTrait::setLogger insteadof LmLoggerAwareTrait;
    }

    /** @var LoggerInterface $logger */
    protected $logger;
}
