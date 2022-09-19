<?php

declare(strict_types=1);

namespace App\Controller\Plugin;

use App\Service\Email as EmailService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

final class Email extends AbstractPlugin
{
    /** @var EmailService $mailService */
    protected $mailService;

    public function __construct(EmailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments): ?EmailService
    {
        return $this->mailService->{$name}(...$arguments);
    }
}
