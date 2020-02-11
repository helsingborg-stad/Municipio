BETA: Blade component library
================

This is a library containing load functions and library of views with their controllers. Enables swift and precise development of static user interfaces across multiple products. The package is designed to be used standalone or with WordPress as CMS engine. 

Important note: This is a view package! Not a single line of CSS or Javascript will be appended. We (will, not complete yet) have a separate package to provide these feature in a syleguide format. 

## Getting started
Recommended method of usage is with composer. Add the requirement below, to enable a new set of awesome features. 

```
composer reqire helsingborg-stad/blade-component-library
```

## Example usage
```php
use BladeComponentLibrary/Register as Register;

class RegisterUtility
{
    public function __construct()
    {
        Register::setCachePath(
            WP_CONTENT_DIR . '/uploads/cache/blade-cache/utility'
        );

        Register::addViewPath(
            MUNICIPIO_PATH . 'views/utility'
        ); 

        Register::addControllerPath(
            MUNICIPIO_PATH . 'library/Controller/Utility/'
        );

        Register::add(
            'button',
            [
                'isPrimary' => true,
                'isDisabled' => false, 
                'isOutlined' => true,

                'label' => "Button text",
                'href' => "https://google.se",

                'target' => "_self"
            ],
            'button.blade.php' // You can leave this out, it will automatically be generated from slug. 
        );

        Register::add(
            'date',
            [
                'hasTime' => false,
                'hasDate' => true, 
                'isHumanReadable' => true
            ],
            'date-time.blade.php'
        );
    }
}
```

## Blade Version
This library uses blade version 5.5 wich requires PHP 7.0. If you like to use a lower version, components will have to be replaced by a publicly avabile function. 

```php
if (!function_exists('component')) {
    /**
     * Get a utility component
     * 
     * @param string $slug       Slug of utility
     * @param array  $attributes The settings of the utility
     * @param string $uid        A unique identifier. Enables WordPress or other filter system to uniquly identify a location of the component to make adjustments to a single component. 
     * 
     * @return string
     */
    function component($slug, $attributes = array(), $uid = "a-unique-id")
    {
       $component = new BladeComponentLibrary\Render($slug, $attributes);
       return $component->render(); 
    }
}
```

You can then untilize the public function in your theme blade files in the following manner. 

```php
{!! 
    utility('button', [
        'isDisabled' => false,
        'label' => "Go to website",
        'href' => "https://helsingborg.se"
        'target' => "_blank"
    ])
!!}
```

## Implement replacement views & controllers
This package is designed to be overrided with a theme or plugins own views. Simply add a new path as below. You have an ability to prepend or append the existing search arrays. The path's will be searched chronologically. 

```php

use BladeComponentLibrary/Register as Register;

//Adds a new view search path
Register::addViewPath(
    MUNICIPIO_PATH . 'views/utility',
    false //Prepend = true
); 

//Adds a new controller search path
Register::addControllerPath(
    MUNICIPIO_PATH . 'library/Controller/Utility/',
    false //Prepend = true
);

```

## Built With 

- PHP 
- Laravel Blade

## Dependencies
- Laravel Blade 5.5 (included in package)

## Releases

https://github.com/helsingborg-stad/blade-component-library/releases

## Authors

- Sebastian Thulin 

## License 

This project is licensed under the MIT License - see the LICENSE file for details