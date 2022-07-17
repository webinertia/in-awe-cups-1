<?php

declare(strict_types=1);

namespace App\Log;

use Laminas\Log\LoggerAwareTrait as LmLoggerAwareTrait;
use Psr\Log\LoggerAwareTrait as PsrLoggerAwareTrait;
use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    // @codingStandardsIgnoreStart
    use LmLoggerAwareTrait, PsrLoggerAwareTrait {
        PsrLoggerAwareTrait::setLogger insteadof LmLoggerAwareTrait;
    }
    // @codingStandardsIgnoreEnd
    /** @var LoggerInterface $logger */
    protected $logger;
}
