<?php

declare(strict_types=1);

namespace App\Controller;

use App\Log\LoggerAwareInterface;
use App\Service\AppSettingsAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use User\Acl\AclAwareInterface;
use User\Service\UserServiceAwareInterface;

interface ControllerInterface extends
    AclAwareInterface,
    AppSettingsAwareInterface,
    TranslatorAwareInterface,
    LoggerAwareInterface,
    ResourceInterface,
    UserServiceAwareInterface
{
}
