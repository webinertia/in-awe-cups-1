<?php

declare(strict_types=1);

namespace App\Service;

interface AppSettingsAwareInterface
{
    public function setAppSettings(array $appSettings);

    public function getAppSettings(): array;
}
