<?php

/**
 * Laminas Logger levels:
 * public const EMERG  = 0;
 * public const ALERT  = 1;
 * public const CRIT   = 2;
 * public const ERR    = 3;
 * public const WARN   = 4;
 * public const NOTICE = 5;
 * public const INFO   = 6;
 * public const DEBUG  = 7;
 */

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Log\Logger;
use Laminas\View\Helper\AbstractHelper;

use function array_key_exists;

class MapLogPriority extends AbstractHelper
{
    /** @var array<int, string> $map */
    protected $map = [
        Logger::EMERG  => 'danger',
        Logger::ALERT  => 'danger',
        Logger::CRIT   => 'danger',
        Logger::ERR    => 'danger',
        Logger::WARN   => 'warning',
        Logger::NOTICE => 'warning',
        Logger::INFO   => 'info',
        Logger::DEBUG  => 'secondary',
    ];

    /** @param string|int $priority */
    public function __invoke($priority = Logger::INFO): string
    {
        $priority = (int) $priority;
        if (array_key_exists($priority, $this->map)) {
            return $this->map[$priority];
        }
        return $this->map[$priority];
    }
}
