<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\BaseForm;
use App\Form\Fieldset\ThemeFieldset;
use App\Model\Theme;
use Laminas\Form\Element\Collection;

use function count;

final class ThemeSettingsForm extends BaseForm
{
    public function __construct(Theme $theme, array $options = [])
    {
        parent::__construct('theme-settings', $options);
        $this->themes = $theme;
    }

    public function init()
    {
        $manager = $this->getFormFactory()->getFormElementManager();
        $config = $this->themes->getConfig();
        foreach ($config as $fieldsetName => $values) {
            $fieldset = new ThemeFieldset($fieldsetName);
            $this->add($fieldset);
            continue;
        }
    }
}
