<?php

declare(strict_types=1);

namespace User\Form;

use App\Form\BaseForm;
use App\Form\FormInterface;
use Laminas\Form\ElementInterface;
use Laminas\Permissions\Acl\AclInterface;
use User\Form\Fieldset\AcctDataFieldset;
use User\Form\Fieldset\PasswordFieldset;
use User\Form\Fieldset\ProfileFieldset;
use User\Form\Fieldset\SocialMediaFieldset;
use User\Service\UserService;

use function array_key_exists;

class ProfileForm extends BaseForm implements FormInterface
{
    public const FORM_NAME = 'profile-manager';

    /** @var AclInterface $acl */
    protected $acl;
    /** @var UserService $userService */
    protected $userService;
    /** @var array<string,class-string<ElementInterface>> $fieldsetMap */
    protected $fieldsetMap = [
        'account-data' => AcctDataFieldset::class,
        'password'     => PasswordFieldset::class,
        'address'      => ProfileFieldset::class,
        'social-media' => SocialMediaFieldset::class,
        'profile'      => ProfileFieldset::class, // this will load all fieldsets
    ];
    /** @return void */
    public function __construct(
        AclInterface $acl,
        UserService $userService,
        ?array $options = []
    ) {
        if ($options === []) {
            $options = [
                'mode' => self::EDIT_MODE,
            ];
        }
        $this->acl         = $acl;
        $this->userService = $userService;
        parent::__construct(self::FORM_NAME, $options);
    }

    public function init(): void
    {
        parent::init();
        $options = $this->getOptions();
        $factory = $this->getFormFactory();
        $manager = $factory->getFormElementManager();
        if (isset($options['action']) && array_key_exists($options['action'], $this->fieldsetMap)) {
            $this->add($manager->build($this->fieldsetMap[$options['action']]), $options);
        }
    }
}
