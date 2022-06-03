<?php

declare(strict_types=1);

namespace Uploader;

final class Module
{
    /**
     * Retrieve default Uploader config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
