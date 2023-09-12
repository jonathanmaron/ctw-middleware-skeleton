<?php
declare(strict_types=1);

namespace CtwTest\Middleware\SkeletonMiddleware;

use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddleware;
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class SkeletonMiddlewareTest extends AbstractCase
{
    public function testSkeletonMiddleware(): void
    {
        $serverParams = [];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->getInstance()];
        $response     = Dispatcher::run($stack, $request);

        self::assertEquals(200, $response->getStatusCode());
    }

    private function getInstance(): SkeletonMiddleware
    {
        $container = new ServiceManager();
        //$container->setService('..', $xx);
        $factory   = new SkeletonMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
