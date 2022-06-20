<?php

declare(strict_types=1);

namespace User\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use User\Service\UserInterface;

final class Identity extends AbstractHelper
{
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function __invoke(): UserInterface
    {
        return $this->userInterface;
    }
}
