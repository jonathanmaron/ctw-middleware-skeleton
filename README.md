# ctw/ctw-middleware-skeleton

[![Build Status](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-skeleton/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-skeleton)

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

