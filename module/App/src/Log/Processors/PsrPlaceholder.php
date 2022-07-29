<?php

declare(strict_types=1);

namespace App\Log\Processors;

use Laminas\Log\Processor\PsrPlaceholder as Placeholder;
use User\Service\UserInterface;

use function array_merge;

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
        if ($event['extra'] === []) {
            $event['extra'] += $this->userInterface->getLogData();
        } elseif ($event['extra'] !== []) {
            $event['extra'] = array_merge($this->userInterface->getLogData(), $event['extra']);
        }
        return parent::process($event);
    }
}
