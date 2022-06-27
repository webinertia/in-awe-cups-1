<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use App\Service\Email as EmailService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

use function call_user_func_array;

final class Email extends AbstractPlugin
{
    /** @var Email $mailService */
    protected $mailService;
    public function __construct(EmailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function __invoke()
    {
        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this->mailService->{$name}(...$arguments);
    }
}
