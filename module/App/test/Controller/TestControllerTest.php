<?php

declare(strict_types=1);

namespace AppTest\Controller;

use App\Controller\TestController;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

final class TestControllerTest extends AbstractHttpControllerTestCase
{
    public function setup(): void
    {
        $configOverrides  = [];
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
        $this->dispatch('/test', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('app');
        $this->assertControllerName(TestController::class); // as specified in router's controller name alias
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('test');
    }

    public function testIndexActionViewModelTemplateRenderedWithinLayout(): void
    {
        $this->dispatch('/', 'GET');
        $this->assertQuery('.container .jumbotron');
    }

    public function testInvalidRouteDoesNotCrash(): void
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
