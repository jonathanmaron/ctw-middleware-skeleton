<?php
declare(strict_types=1);

namespace CtwTest\Middleware\SkeletonMiddleware;

use Ctw\Middleware\SkeletonMiddleware\ConfigProvider;
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddleware;
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddlewareFactory;

class ConfigProviderTest extends AbstractCase
{
    public function testConfigProvider(): void
    {
        $configProvider = new ConfigProvider();

        $expected = [
            'dependencies' => [
                'factories' => [
                    SkeletonMiddleware::class => SkeletonMiddlewareFactory::class,
                ],
            ],
        ];

        $this->assertSame($expected, $configProvider->__invoke());
    }
}
