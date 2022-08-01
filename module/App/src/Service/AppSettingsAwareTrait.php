<?php

declare(strict_types=1);

namespace App\Service;

trait AppSettingsAwareTrait
{
    /** @var array<string, mixed> $appSettings */
    protected $appSettings;
    /** @param array<string, int|bool|string> $appSettings */
    public function setAppSettings(array $appSettings = []): void
    {
        $this->appSettings = $appSettings;
    }

    /** @return array<string, mixed> */
    public function getAppSettings(): array
    {
        if (isset($this->config) && isset($this->config['app_settings'])) {
            $this->setAppSettings($this->config['app_settings']);
        } else {
            $this->setAppSettings();
        }
        return $this->appSettings;
    }
}
