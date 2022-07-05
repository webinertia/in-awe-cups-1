<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\ModelInterface;
use Laminas\Config\Factory;

use function dirname;

final class Theme implements ModelInterface
{
    public const DEFAULT_THEME = 'default';
    /** @var string $currentTheme */
    protected $activeTheme;
    /** @var string $fallBack */
    protected $fallBack;
    /** @var string $configPath */
    protected $configPath;
    /** @var string $configFilename */
    protected $configFilename = 'themes.php';
    /** @var string $directory */
    protected $directory;
    /** @var array $paths */
    protected $paths = [];

    public function __construct()
    {
        $this->configPath = dirname(__DIR__, 4) . '/config/';
        $this->directory  = dirname(__DIR__, 4) . '/theme/';
        $this->config     = Factory::fromFile($this->configPath . $this->configFilename);
        $this->processConfig($this->config);
    }

    protected function processConfig(array $config)
    {
        foreach ($config as $theme) {
            if ($theme['active']) {
                $this->setActiveTheme($theme['name']);
            }
            if (! empty($theme['fallback'])) {
                $this->setFallBack($theme['fallback']);
            }
        }
    }

    public function getConfigPath(): string
    {
        return $this->configPath . $this->configFilename;
    }

    public function getTemplateMap()
    {
    }

    public function getThemePaths(): array
    {
        if ($this->activeTheme === self::DEFAULT_THEME) {
            $this->paths = [$this->directory . self::DEFAULT_THEME];
        } elseif ($this->activeTheme !== self::DEFAULT_THEME && $this->fallBack === self::DEFAULT_THEME) {
            $this->paths = [
                $this->directory . self::DEFAULT_THEME,
                $this->directory . $this->activeTheme,
            ];
        } elseif ($this->activeTheme !== self::DEFAULT_THEME && $this->fallBack !== self::DEFAULT_THEME) {
            $this->paths = [
                $this->directory . $this->fallBack,
                $this->directory . $this->activeTheme,
            ];
        }
        return $this->paths;
    }

    /** @param string $activeTheme */
    public function setActiveTheme($activeTheme): void
    {
        $this->activeTheme = $activeTheme;
    }

    public function getActiveTheme(): string
    {
        return $this->activeTheme;
    }

    /** @param string $fallBack */
    public function setFallBack($fallBack)
    {
        $this->fallBack = $fallBack;
    }

    public function getFallBack(): string
    {
        return $this->fallBack;
    }

    public function getResourceId(): string
    {
        return 'theme';
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
