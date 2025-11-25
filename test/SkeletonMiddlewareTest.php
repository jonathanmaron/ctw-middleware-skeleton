<?php
declare(strict_types=1);

namespace CtwTest\Middleware\SkeletonMiddleware;

use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddleware;
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Server\MiddlewareInterface;

final class SkeletonMiddlewareTest extends AbstractCase
{
    /**
     * Test that middleware returns 200 status code
     */
    public function testSkeletonMiddleware(): void
    {
        $serverParams = [];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that middleware implements MiddlewareInterface
     */
    public function testMiddlewareImplementsMiddlewareInterface(): void
    {
        $middleware = $this->getInstance();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that middleware passes request to handler
     */
    public function testMiddlewarePassesRequestToHandler(): void
    {
        $handlerCalled = false;
        $stack         = [
            $this->getInstance(),
            /**
             * @param mixed $request
             * @param mixed $next
             * @return \Psr\Http\Message\ResponseInterface
             */
            static function ($request, $next) use (&$handlerCalled) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $next */
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $handlerCalled = true;

                return $next->handle($request);
            },
        ];
        Dispatcher::run($stack);

        self::assertTrue($handlerCalled);
    }

    /**
     * Test that middleware preserves handler response
     */
    public function testMiddlewarePreservesHandlerResponse(): void
    {
        $stack = [
            $this->getInstance(),
            /**
             * @param mixed $request
             * @param mixed $next
             * @return \Psr\Http\Message\ResponseInterface
             */
            static function ($request, $next) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $next */
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $response = $next->handle($request);

                return $response->withHeader('X-Custom', 'value');
            },
        ];
        $response = Dispatcher::run($stack);

        self::assertTrue($response->hasHeader('X-Custom'));
        self::assertSame('value', $response->getHeaderLine('X-Custom'));
    }

    /**
     * Test that middleware preserves handler response status code
     */
    public function testMiddlewarePreservesHandlerResponseStatusCode(): void
    {
        $stack = [
            $this->getInstance(),
            /**
             * @param mixed $request
             * @param mixed $next
             * @return \Psr\Http\Message\ResponseInterface
             */
            static function ($request, $next) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $next */
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $response = $next->handle($request);

                return $response->withStatus(201);
            },
        ];
        $response = Dispatcher::run($stack);

        self::assertSame(201, $response->getStatusCode());
    }

    /**
     * Test that factory creates middleware instance
     */
    public function testFactoryCreatesMiddlewareInstance(): void
    {
        $container  = new ServiceManager();
        $factory    = new SkeletonMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(SkeletonMiddleware::class, $middleware);
    }

    /**
     * Test various HTTP methods
     *
     * @return array<string, array{method: string}>
     */
    public static function httpMethodProvider(): array
    {
        return [
            'GET request'    => [
                'method' => 'GET',
            ],
            'POST request'   => [
                'method' => 'POST',
            ],
            'PUT request'    => [
                'method' => 'PUT',
            ],
            'DELETE request' => [
                'method' => 'DELETE',
            ],
            'PATCH request'  => [
                'method' => 'PATCH',
            ],
        ];
    }

    /**
     * Test that middleware works with various HTTP methods
     */
    #[DataProvider('httpMethodProvider')]
    public function testMiddlewareWorksWithVariousHttpMethods(string $method): void
    {
        $request  = Factory::createServerRequest($method, '/');
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test various URI paths
     *
     * @return array<string, array{path: string}>
     */
    public static function pathProvider(): array
    {
        return [
            'root path'      => [
                'path' => '/',
            ],
            'simple path'    => [
                'path' => '/api',
            ],
            'nested path'    => [
                'path' => '/api/v1/users',
            ],
            'with query'     => [
                'path' => '/search?q=test',
            ],
        ];
    }

    /**
     * Test that middleware works with various paths
     */
    #[DataProvider('pathProvider')]
    public function testMiddlewareWorksWithVariousPaths(string $path): void
    {
        $request  = Factory::createServerRequest('GET', $path);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that request attributes are preserved
     */
    public function testRequestAttributesArePreserved(): void
    {
        $request = Factory::createServerRequest('GET', '/')
            ->withAttribute('test', 'value');

        $capturedAttribute = null;
        $stack             = [
            $this->getInstance(),
            /**
             * @param mixed $request
             * @param mixed $next
             * @return \Psr\Http\Message\ResponseInterface
             */
            static function ($request, $next) use (&$capturedAttribute) {
                /** @var \Psr\Http\Server\RequestHandlerInterface $next */
                /** @var \Psr\Http\Message\ServerRequestInterface $request */
                $capturedAttribute = $request->getAttribute('test');

                return $next->handle($request);
            },
        ];
        Dispatcher::run($stack, $request);

        self::assertSame('value', $capturedAttribute);
    }

    /**
     * Test that multiple middleware instances can be chained
     */
    public function testMultipleMiddlewareInstancesCanBeChained(): void
    {
        $stack = [$this->getInstance(), $this->getInstance(), $this->getInstance()];
        $response = Dispatcher::run($stack);

        self::assertSame(200, $response->getStatusCode());
    }

    private function getInstance(): SkeletonMiddleware
    {
        $container = new ServiceManager();
        $factory   = new SkeletonMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
