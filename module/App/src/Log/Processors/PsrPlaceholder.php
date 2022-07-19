<?php

declare(strict_types=1);

namespace App\Log\Processors;

use Laminas\Log\Processor\PsrPlaceholder as Placeholder;
use User\Service\UserInterface;

final class PsrPlaceholder extends Placeholder
{
    /** @var UserInterface userInterface */
    protected $userInterface;

    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function process(array $event): array
    {
        $event['extra'] += $this->userInterface->getLogData();
        return parent::process($event);
    }
}
