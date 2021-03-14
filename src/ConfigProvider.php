<?php
declare(strict_types=1);

namespace Ctw\Middleware\SkeletonMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                SkeletonMiddleware::class => SkeletonMiddlewareFactory::class,
            ],
        ];
    }
}
