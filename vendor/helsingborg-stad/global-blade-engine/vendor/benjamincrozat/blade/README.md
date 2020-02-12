[![Build Status](https://travis-ci.org/benjamincrozat/blade.svg?branch=master)](https://travis-ci.org/benjamincrozat/blade)
[![Latest Stable Version](https://poser.pugx.org/benjamincrozat/blade/v/stable)](https://packagist.org/packages/benjamincrozat/blade)
[![License](https://poser.pugx.org/benjamincrozat/blade/license)](https://packagist.org/packages/benjamincrozat/blade)
[![Total Downloads](https://poser.pugx.org/benjamincrozat/blade/downloads)](https://packagist.org/packages/benjamincrozat/blade)

# Blade

Use [Laravel Blade](https://laravel.com/docs/blade) in any PHP project. The adapter class is clean and I don't make use of unecessary Laravel related dependencies.

**If you don't know about Blade yet, please refer to the [official documentation](https://laravel.com/docs/blade).**

## Requirements

- PHP 5.6+

## Installation

```php
composer require benjamincrozat/blade
```

## Usage

This package allows you to do almost everything you were able to do in a Laravel project.

Here is a basic view rendering:

```php
use BC\Blade\Blade;

$blade = new Blade(__DIR__ . '/views', __DIR__ . '/cache');

echo $blade->make('home', ['foo' => 'bar'))->render();
```

Add the `@hello('John')` directive:

```php
$blade->directive('hello', function ($expression) {
    $expression = trim($expression, '\'"');

    return '<?php echo "Hello $expression!"; ?>';
});
```

Make a variable available in all views thanks to view composers:

```php
$blade->composer('*', function ($view) {
    $view->with(['foo' => 'bar']);
});
```

... and so on. Just use Blade as you are used to.

Enjoy!

## License

[MIT](http://opensource.org/licenses/MIT)
