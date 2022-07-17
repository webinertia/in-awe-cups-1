<?php

declare(strict_types=1);

namespace ContentManager\Form;

use App\Form\BaseForm;
use App\Form\FormInterface;
use ContentManager\Form\Fieldset\PageFieldset;
use User\Db\UserGateway as User;

final class PageForm extends BaseForm
{
    /**
     * @param array $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(
        User $user,
        array $appSettings,
        $options = []
    ) {
        $this->user        = $user;
        $this->appSettings = $appSettings;
        parent::__construct('page-form');
        if (! empty($options) && isset($options['mode'])) {
            parent::setOptions($options);
        } elseif (empty($options) || ! empty($options) && ! isset($options['mode'])) {
            $options['mode'] = FormInterface::CREATE_MODE;
            parent::setOptions($options);
        }
    }

    public function init(): void
    {
        $options = $this->getOptions();
        $factory = $this->getFormFactory();
        $manager = $factory->getFormElementManager();
        $this->add($manager->build(PageFieldset::class, ['mode' => $options['mode']]));
    }
}
