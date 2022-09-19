<?php

declare(strict_types=1);

namespace AppTest\Controller;

use App\Controller\IndexController;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

final class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.

        $configOverrides  = [
            'db' => [
                'driver'   => 'pdo_mysql',
                'dsn'      => 'mysql:dbname=aurora;host=localhost;charset=utf8',
                'username' => 'aurora',
                'password' => 'password',
            ],
        ];
        $configOverrides += include __DIR__ . '/../../../../config/roles.php';
        $configOverrides += include __DIR__ . '/../../../../config/themes.php';
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            include __DIR__ . '/../../../../config/autoload/appsettings.global.php',
            $configOverrides
        ));
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed(): void
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('app');
        $this->assertControllerName(IndexController::class); // as specified in router's controller name alias
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }

    public function testIndexActionViewModelTemplateRenderedWithinLayout(): void
    {
        $this->dispatch('/', 'GET');
        $this->assertQuery('.container .jumbotron');
    }

    public function testContactActionFormRenderedWithViewFile(): void
    {
        $this->dispatch('/site/contact', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('app');
        $this->assertControllerName(IndexController::class); // as specified in router's controller name alias
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('contact');
        $this->assertQuery('form');
    }

    public function testActionCanHandlePost(): void
    {
        $this->dispatch(
            '/site/contact',
            'POST',
            ['fullName' => 'Test User', 'email' => 'test@test.com', 'message' => 'testing']
        );
        $this->assertModuleName('app');
        $this->assertControllerName(IndexController::class);
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('contact');
        $this->assertRedirectTo('/');
    }

    public function testInvalidRouteDoesNotCrash(): void
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
