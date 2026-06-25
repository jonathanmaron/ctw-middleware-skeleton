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
     * Test that the middleware yields a 200 response when dispatched on a basic GET request.
     */
    public function testProcessReturnsSuccessfulResponse(): void
    {
        $serverParams = [];
        $request  = Factory::createServerRequest('GET', '/', $serverParams);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that the middleware implements MiddlewareInterface when instantiated through the factory.
     */
    public function testMiddlewareImplementsMiddlewareInterface(): void
    {
        $middleware = $this->getInstance();

        // @phpstan-ignore-next-line
        self::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * Test that the middleware delegates to the next request handler in the stack.
     */
    public function testProcessDelegatesRequestToNextHandler(): void
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
     * Test that headers set by a downstream handler are preserved by the middleware.
     */
    public function testProcessPreservesDownstreamResponseHeaders(): void
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
     * Test that the status code set by a downstream handler is preserved by the middleware.
     */
    public function testProcessPreservesDownstreamResponseStatusCode(): void
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
     * Test that the factory produces a SkeletonMiddleware instance from the container.
     */
    public function testFactoryCreatesSkeletonMiddlewareInstance(): void
    {
        $container  = new ServiceManager();
        $factory    = new SkeletonMiddlewareFactory();
        $middleware = $factory($container);

        // @phpstan-ignore-next-line
        self::assertInstanceOf(SkeletonMiddleware::class, $middleware);
    }

    /**
     * Provides representative HTTP methods exercised by the middleware.
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
     * Test that the middleware returns a 200 response regardless of the request HTTP method.
     */
    #[DataProvider('httpMethodProvider')]
    public function testProcessReturnsSuccessForAnyHttpMethod(string $method): void
    {
        $request  = Factory::createServerRequest($method, '/');
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Provides representative URI paths exercised by the middleware.
     *
     * @return array<string, array{path: string}>
     */
    public static function pathProvider(): array
    {
        return [
            'root path'   => [
                'path' => '/',
            ],
            'simple path' => [
                'path' => '/api',
            ],
            'nested path' => [
                'path' => '/api/v1/users',
            ],
            'with query'  => [
                'path' => '/search?q=test',
            ],
        ];
    }

    /**
     * Test that the middleware returns a 200 response regardless of the request URI path.
     */
    #[DataProvider('pathProvider')]
    public function testProcessReturnsSuccessForAnyRequestPath(string $path): void
    {
        $request  = Factory::createServerRequest('GET', $path);
        $stack    = [$this->getInstance()];
        $response = Dispatcher::run($stack, $request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that request attributes set before the middleware reach the downstream handler intact.
     */
    public function testProcessPreservesRequestAttributesForHandler(): void
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
     * Test that several stacked instances of the middleware still yield a 200 response.
     */
    public function testProcessSucceedsWhenMultipleInstancesAreChained(): void
    {
        $stack    = [$this->getInstance(), $this->getInstance(), $this->getInstance()];
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
