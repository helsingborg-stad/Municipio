Global Blade Engine
================

Creates a single instance of benjamincrozat/blade to be used in multiple packages and project running side-by-side. 

## Known issues
- View names must be unique to whole project. For now the solution is to set view path one level above the target dir and make the name unique. You calls to view will be prefixed with this folder name. Eg. @include("uniquename.viewname")

## Getting started
Recommended method of usage is with composer. Add the requirement below, to enable a new set of awesome features. 

```
composer reqire helsingborg-stad/global-blade-engine
```

## Example usage (register a component)
```php
use \HelsingborgStad\GlobalBladeEngine as Blade;  

Blade::addViewPath(BASEPATH . 'views');

echo Blade::instance()->make($view, $data)->render();
```

## Built With 
- Laravel Blade 5.8 based on standalone version benjamincrozat/blade

## Dependencies
- PHP 7.3

## Releases

https://github.com/helsingborg-stad/blade-component-library/releases

## Authors

- Sebastian Thulin 

## License 

This project is licensed under the MIT License - see the LICENSE file for details