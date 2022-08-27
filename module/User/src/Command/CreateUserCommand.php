<?php

declare(strict_types=1);

namespace User\Command;

use App\Log\LogEvent;
use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\ParamAwareInputInterface;
use Laminas\Cli\Input\IntParam;
use Laminas\Cli\Input\StringParam;
use Laminas\Db\Adapter\Adapter;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use User\Service\UserService;
use User\Service\UserServiceInterface;

use function password_hash;

use const PASSWORD_DEFAULT;

class CreateUserCommand extends AbstractParamAwareCommand
{

    /** @var array<string, mixed> $config */
    private $config;
    /** @var UserService $userService */
    private $userService;
    public function __construct(
        Adapter $dbAdapter,
        array $config,
    ) {
        parent::__construct();
        $this->dbAdapter = $dbAdapter;
        $this->config    = $config;
    }

    public function configure(): void
    {
        $this->setName('create-user');
        $this->setDescription('Create a new user');
        $this->setHelp('This command creates a new user');
        $this->addParam(
            (new StringParam('userName'))->setDescription('The username of the new user')
        );
        $this->addParam(
            (new StringParam('password'))->setDescription('The password of the new user')
        );
        $this->addParam(
            (new StringParam('role'))->setDescription('The role of the user (full group name)')
        );
        $this->addParam(
            (new StringParam('firstName'))->setDescription('The first name of the new user')
        );
        $this->addParam(
            (new StringParam('lastName'))->setDescription('The last name of the user')
        );
        $this->addParam(
            (new StringParam('active'))->setDescription('Activate the account?')->setDefault(1)
        );
    }

    /** @param ParamAwareInputInterface $input */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userName = $input->getParam('userName');
        $password = password_hash($input->getParam('password'), PASSWORD_DEFAULT);
        $role     = $input->getParam('role');
        return 0;
    }
}
