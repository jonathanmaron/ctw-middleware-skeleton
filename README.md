# Package "ctw/ctw-middleware-skeleton"

[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-skeleton/actions/workflows/tests.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-skeleton/actions/workflows/tests.yml)
[![Scrutinizer Build](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/build-status/master)
[![Scrutinizer Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/?branch=master)

```bash
$ composer require ctw/ctw-middleware-skeleton
```

Intro

[middlewares/utils](https://packagist.org/packages/middlewares/utils) provides utility classes for working with PSR-15.

## Installation

Install the middleware using Composer:

```bash
$ composer require ctw/ctw-middleware-skeleton
```

## Standalone Example

```php
// standalone example
```

## Example in [Mezzio](https://docs.mezzio.dev/)

The middleware has been extensively tested in Mezzio.

After using Composer to install, simply make the following changes to your application's configuration.

In `config/config.php`:

```php
$providers = [
    // [..]
    \Ctw\Middleware\SkeletonMiddleware\ConfigProvider::class,
    // [..]    
];
```

In `config/pipeline.php`:

```php
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddleware;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    // [..]
    $app->pipe(SkeletonMiddleware::class);
    // [..]
};
```
