<?php

declare(strict_types=1);

namespace ContentManager\Form;

use Application\Form\BaseForm;
use Application\Form\FormInterface;
use Application\Model\Settings;
use ContentManager\Form\Fieldset\PageFieldset;
use Laminas\Authentication\AuthenticationService;
use User\Model\Users;
use User\Permissions\PermissionsManager;

final class PageForm extends BaseForm
{
    /**
     * @param array $options
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(
        AuthenticationService $auth,
        PermissionsManager $pm,
        Users $usrModel,
        Settings $appSettings,
        $options = []
    ) {
        $this->auth        = $auth;
        $this->pm          = $pm;
        $this->usrModel    = $usrModel;
        $this->appSettings = $appSettings;
        parent::__construct('page-form');
        if (! empty($options) && isset($options['mode'])) {
            parent::setOptions($options);
        } elseif (empty($options) || ! empty($options) && ! isset($options['mode'])) {
            $options['mode'] = FormInterface::CREATE_MODE;
            parent::setOptions($options);
        }
    }

    public function init()
    {
        $options = $this->getOptions();
        $factory = $this->getFormFactory();
        $manager = $factory->getFormElementManager();
        $this->add($manager->build(PageFieldset::class, ['mode' => $options['mode']]));
    }
}
