<?php

/**
 * This file is for general testing to prevent random changes in other controllers
 * phpcs:ignoreFile
 */

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractAppController;
use App\Controller\Trait\JsonDataTrait;
use App\Filter\FqcnToControllerName;
use DirectoryIterator;
use ReflectionClass;
use ReflectionMethod;
use App\Log\LoggerAwareInterface;
use Laminas\View\Model\ViewModel;
use Laminas\Filter\StringToLower;
use Laminas\Filter\FilterChain;
use User\Acl\ResourceAwareTrait;
use Webinertia\Utils\Debug;

use function strpos;
use function str_replace;

final class TestController extends AbstractAppController implements LoggerAwareInterface
{
    use JsonDataTrait;
    use ResourceAwareTrait;

    /** @var string $resourceId */
    protected $resourceId = 'test';
    public function indexAction(): ViewModel
    {
        //Debug::dump($_SESSION);
        $stripString = 'Action';
        $filterChain = new FilterChain();
        $fqcnFilter  = new FqcnToControllerName();
        $filterChain->attach($fqcnFilter)->attach(new StringToLower());
        $path = __DIR__ . '/../../../../module/User/src/Controller';
        $dir = new DirectoryIterator($path);
        $targetNamespace = 'User\\Controller\\';
        $classNames    = [];
        $resources     = [];
        $actionNames   = [];
        foreach ($dir as $file) {
            if ($file->isDot() && $file->getFileName()) {
                continue;
            }
            if (! $file->isDir()) {
                $parts = explode('.', $file->getFilename());
                $class = $parts[0];
                $reflClass = new ReflectionClass($targetNamespace . $class);
                $methods = $reflClass->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $declaringClass = $method->getDeclaringClass()->getName();
                    $currentContext = $reflClass->getName() === $declaringClass;
                    if ($method->isPublic() && ! $method->isConstructor() && $currentContext) {
                        $classNames[]  = $method->getDeclaringClass()->getName();
                        $actionNames[] = $method->getName();
                        if (strpos($method->getName(), $stripString)) {
                            $resourceName = str_replace($stripString, '', $method->getName());
                        } else {
                            $resourceName = $method->getName();
                        }
                        $resources[] = $filterChain->filter($reflClass->getName()) . '.' . $resourceName;
                    }
                }
            }
        }
       // Debug::dump($resources, 'resources');
       // Debug::dump($actionNames, 'action names');
        return $this->view;
    }
}
