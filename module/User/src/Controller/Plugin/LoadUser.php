<?php

declare(strict_types=1);

namespace User\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Service\UserInterface;

final class LoadUser extends AbstractPlugin
{
    /** @var UserInterface $userInterface */
    protected $userInterface;

    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    /**
     * @param bool $returnObject
     * @return mixed
     */
    public function __invoke($returnObject = true)
    {
        if ($returnObject) {
            return $this->userInterface;
        } else {
            return $this->userInterface->getArrayCopy();
        }
    }
}
