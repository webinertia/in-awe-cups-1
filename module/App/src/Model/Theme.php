<?php

declare(strict_types=1);

namespace App\Model;

use Webinertia\ModelManager\AbstractModel;

use function dirname;

class Theme extends AbstractModel
{
    public const DEFAULT_THEME = 'default';
    /** @var string $currentTheme */
    protected $currentTheme = 'default';
    /** @var string $basedOn */
    protected $basedOn = 'default';

    public function getCurrentTheme(): string
    {
        return $this->currentTheme;
    }

    public function getTemplateMap()
    {
    }

    public function getThemePaths(): array
    {
        if ($this->currentTheme === self::DEFAULT_THEME) {
            return [dirname(__DIR__, 4) . '/theme/default'];
        } elseif ($this->currentTheme !== self::DEFAULT_THEME && $this->basedOn === self::DEFAULT_THEME) {
            return [
                dirname(__DIR__, 4) . '/theme/default',
                dirname(__DIR__, 4) . '/theme/' . $this->currentTheme,
            ];
        }
    }
}
