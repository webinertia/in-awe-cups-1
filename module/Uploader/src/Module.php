<?php

declare(strict_types=1);

namespace Uploader;

class Module
{
    /**
     * Retrieve default Uploader config.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
