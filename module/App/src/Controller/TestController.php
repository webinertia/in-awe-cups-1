<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use Laminas\View\Model\ViewModel;
use Webinertia\Utils\Debug;
use Laminas\Http\PhpEnvironment\Request as PhpRequest;

final class TestController extends AbstractAppController
{
    public function indexAction(): ViewModel
    {
        //Debug::dump($_SESSION, '$_SESSION');
        //Debug::dump($this->identity()->hasIdentity(), '$this->identity()->hasIdentity()');
        $phpRequest = $this->service()->get(PhpRequest::class);
        $config = $this->service()->get('config')['session_config'];
        Debug::dump($phpRequest->getServer(), '$phpRequest->getServer()->get("REQUEST_URI")');
        $scheme = 'https';
        if (
            $scheme === 'https' &&
            ! $config['session_config']['cookie_secure']
        ) {
            $config['session_config']['cookie_secure'] = true;
        }


        return $this->view;
    }
}
