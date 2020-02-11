# Laravel Blade Standalone Package
This is a fork of the fantastic work by Philo Hermans. It's purpose is to provide with an up to date version of laravel blade package as a standalone version. From version 5.5 and forward the versioning system will mimic the version of the native versioning of laravel. To get version 5.8 of laravel, simple request it here and you will get that. No more figuring out what version that represents what in laravel (patches will not follow this).


### Installation (Blade Laravel 5.8) - Released: 2019-03-22
The package can be installed via Composer by requiring the "helsingborg-stad/laravel-blade": "5.8" package in your project's composer.json. 

```json
{
	"require": {
	    "helsingborg-stad/laravel-blade": "5.8"
	}
}
```

### Installation (Blade Laravel 5.5) - Not yet released
The package can be installed via Composer by requiring the "helsingborg-stad/laravel-blade": "5.5" package in your project's composer.json.

```json
{
	"require": {
	    "helsingborg-stad/laravel-blade": "5.5"
	}
}
```

### Installation (older versions)
Please use the origin package for older version. Package can be found here: https://github.com/PhiloNL/Laravel-Blade 

### Usage

```php
<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require 'vendor/autoload.php';

use HelsingborgStad\Blade\Blade;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';

$blade = new Blade($views, $cache);
echo $blade->view()->make('hello')->render();
```

You can use all blade features as described in the Laravel 5.1 documentation:
https://laravel.com/docs/5.1/blade
