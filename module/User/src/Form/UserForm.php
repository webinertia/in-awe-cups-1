<?php

/**
 * @usage In most cases this class should be built by the FormElementManager instead
 * of an instance being retrieved via get
 * Developers should pass $options['mode'] key so as to hint to the
 * form which context to build the fieldsets in
 */

declare(strict_types=1);

namespace User\Form;

use Application\Form\BaseForm;
use Application\Form\Fieldset\SecurityFieldset;
use Application\Form\FormInterface;
use Application\Model\Settings;
use Laminas\Authentication\AuthenticationService;
use Laminas\Form\Exception\InvalidArgumentException;
use User\Form\Fieldset\AcctDataFieldset;
use User\Form\Fieldset\PasswordFieldset;
use User\Form\Fieldset\ProfileFieldset;
use User\Form\Fieldset\RoleFieldset;
use User\Model\Users;
use User\Permissions\PermissionsManager;

class UserForm extends BaseForm
{
    /** @var AuthenticationService $auth */
    protected $auth;
    /** @var PermissionsManager $pm */
    protected $pm;
    /** @var User\Model\Users $usrModel */
    protected $usrModel;
    /** @var Settings $appSettings */
    protected $appSettings;
    /** @var string $mode */
    protected $mode;

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
        parent::__construct('user-form');
        if (! empty($options) && isset($options['mode'])) {
            parent::setOptions($options);
        } elseif (empty($options) || ! empty($options) && ! isset($options['mode'])) {
            $options['mode'] = FormInterface::CREATE_MODE;
            parent::setOptions($options);
        }
    }

    public function init(): void
    {
        // get the options, notice that we set a default in the __construct
        $options = $this->getOptions();
        $factory = $this->getFormFactory();
        $manager = $factory->getFormElementManager();
        $secData = $manager->build(SecurityFieldset::class, ['mode' => $options['mode']]);
        $this->add($secData, ['priority' => -1]);
        $acctData = $manager->build(AcctDataFieldset::class, ['mode' => $options['mode']]);
        $this->add($acctData, ['priority' => 1]);
        // we will use this in both mode(s), but only if the user is already logged in and has privilege
        if ($this->auth->hasIdentity() && $this->pm->isAllowed($this->usrModel, $this->usrModel, 'admin.access')) {
            $this->add([
                'type' => RoleFieldset::class,
            ]);
        }
        // build a context aware instance of the fieldset so as to know if we need an id input...
        // we only need this for edit, since we will be using the id input from acct-data otherwise
        $profileData = $manager->build(ProfileFieldset::class, ['mode' => $options['mode']]);
        $this->add($profileData);

        if (isset($options['mode']) && $options['mode'] === FormInterface::CREATE_MODE) {
            $this->add([
                'type' => PasswordFieldset::class,
                ['priority' => 2],
            ]);
        }
    }
}
