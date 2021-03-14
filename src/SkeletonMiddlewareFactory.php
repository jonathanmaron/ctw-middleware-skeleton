<?php
declare(strict_types=1);

namespace Ctw\Middleware\SkeletonMiddleware;

use Psr\Container\ContainerInterface;

class SkeletonMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): SkeletonMiddleware
    {
        return new SkeletonMiddleware();
    }
}
