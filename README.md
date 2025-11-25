# Package "ctw/ctw-middleware-skeleton"

[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-skeleton/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-skeleton)
[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-skeleton/actions/workflows/tests.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-skeleton/actions/workflows/tests.yml)
[![Scrutinizer Build](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/build-status/master)
[![Scrutinizer Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-skeleton/?branch=master)

Skeleton package providing a template structure for creating new PSR-15 middleware packages in the ctw/ctw-middleware-* family.

## Introduction

### Why This Library Exists

Creating new middleware packages from scratch involves repetitive boilerplate: directory structure, abstract classes, factories, config providers, PHPUnit configuration, and CI workflows. This skeleton provides a ready-to-use template that ensures consistency across all ctw middleware packages.

The skeleton includes:

- **Standard directory layout**: Organized `src/` and `test/` structure matching other ctw packages
- **PSR-15 compliant base**: Extends `AbstractMiddleware` from ctw/ctw-middleware
- **Factory pattern**: Ready-to-use factory for dependency injection containers
- **ConfigProvider**: Mezzio-compatible configuration provider for service registration
- **QA tooling**: Pre-configured for ECS, PHPStan, Rector, and PHPUnit

### Problems This Library Solves

1. **Inconsistent structure**: New packages may differ in layout and organization
2. **Missing boilerplate**: Forgetting essential files like ConfigProvider or factories
3. **Repeated setup**: Copying and modifying existing packages is error-prone
4. **QA configuration drift**: Different packages may use different tool configurations
5. **Learning curve**: New contributors need a reference for package structure

### Where to Use This Library

- **New middleware development**: Clone and rename to start a new middleware package
- **Reference implementation**: Study the structure when creating custom middleware
- **Template source**: Copy files as needed for non-ctw PSR-15 middleware projects
- **Testing base**: Verify your development environment works with a minimal package

### Design Goals

1. **Minimal implementation**: Pass-through middleware with no functionality to modify
2. **Complete structure**: All required files for a production-ready package
3. **Consistent patterns**: Matches conventions used across all ctw middleware packages
4. **Easy customization**: Clear placeholders and naming for search-and-replace
5. **Ready for CI**: GitHub Actions workflow and QA tools pre-configured

## Requirements

- PHP 8.3 or higher
- ctw/ctw-middleware ^4.0

## Installation

Install by adding the package as a [Composer](https://getcomposer.org) requirement:

```bash
composer require ctw/ctw-middleware-skeleton
```

## Usage Examples

### Creating a New Middleware Package

1. Clone the skeleton repository
2. Rename files and namespaces from `Skeleton` to your middleware name
3. Update `composer.json` with your package name and description
4. Implement your middleware logic in the `process()` method

### Package Structure

```
ctw-middleware-skeleton/
├── src/
│   ├── AbstractSkeletonMiddleware.php
│   ├── ConfigProvider.php
│   ├── SkeletonMiddleware.php
│   └── SkeletonMiddlewareFactory.php
├── test/
│   └── ...
├── composer.json
├── ecs.php
├── phpstan.neon
├── phpunit.xml.dist
├── rector.php
└── README.md
```

### File Purposes

| File | Purpose |
|------|---------|
| `AbstractSkeletonMiddleware.php` | Base class for shared functionality |
| `SkeletonMiddleware.php` | Main middleware implementation |
| `SkeletonMiddlewareFactory.php` | Creates middleware instances via DI container |
| `ConfigProvider.php` | Registers services with Mezzio/Laminas |

### Basic Middleware Implementation

The skeleton provides a pass-through implementation:

```php
class SkeletonMiddleware extends AbstractSkeletonMiddleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $handler->handle($request);
    }
}
```

### Pipeline Registration (Mezzio)

```php
use Ctw\Middleware\SkeletonMiddleware\SkeletonMiddleware;

// In config/pipeline.php
$app->pipe(SkeletonMiddleware::class);
```

### ConfigProvider Registration

```php
// config/config.php
return [
    // ...
    \Ctw\Middleware\SkeletonMiddleware\ConfigProvider::class,
];
```

### Customization Checklist

When creating a new middleware from this skeleton:

- [ ] Rename namespace from `SkeletonMiddleware` to `YourMiddleware`
- [ ] Update class names in all files
- [ ] Modify `composer.json` (name, description, autoload)
- [ ] Implement middleware logic in `process()` method
- [ ] Add configuration options if needed (via factory)
- [ ] Write tests for your implementation
- [ ] Update README.md with your middleware documentation
