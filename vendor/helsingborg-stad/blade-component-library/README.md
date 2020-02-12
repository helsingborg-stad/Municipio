BETA: Blade component library
================

This is a library containing load functions and library of views with their controllers. Enables swift and precise development of static user interfaces across multiple products. The package is designed to be used standalone or with WordPress as CMS engine. 

Important note: This is a view package! Not a single line of CSS or Javascript will be appended. We (will, not complete yet) have a separate package to provide these feature in a syleguide format. 

## Getting started
Recommended method of usage is with composer. Add the requirement below, to enable a new set of awesome features. 

```
composer reqire helsingborg-stad/blade-component-library
```

## Known issues
- Components/directives must be called with an @endcomponent tag. Directives without end-tag will not work. 

## Example usage (register a component)
```php
<?php

namespace Municipio\Theme;

class RegisterUtility
{
    public function __construct()
    {
        \BladeComponentLibrary\Register::addViewPath(
            MUNICIPIO_PATH . 'views/utility',
            true //true = prepend, false = append, default = prepend
        ); 

        \BladeComponentLibrary\Register::addControllerPath(
            MUNICIPIO_PATH . 'library/Controller/Utility/',
            true //true = prepend, false = append, default = prepend
        );

        \BladeComponentLibrary\Register::add(
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

        \BladeComponentLibrary\Register::add(
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

## Example usage (render component)
A registered component can be utilized as a component or directive just as in laravel. They do however have the added functionality to load the controller before render to enshure that stuff is formatted and defined.

### Render as a directive
```php
@button(['text' => "Button text", 'href' => "https://helsingborg.se"]); 
```

### Render as a component
```php
@component('button')
    
    Button text

    @slot('href')
        https://helsingborg.se
    @endslot

@endcomponent
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

## Data flow
Base controller components
This controller handles all data flow to every component. 
There are multiple ways of inputting data to a component. 
  
1. The default configuration of the component.
These settings are made in the configuration json 
in each component folder. All variables used in the 
controller SHOULD be declared here. This is to
avoid undeclared variabe varnings. 
 
2. By populating the directive (in view file). 
This should be data that idicates states like
isDisabled => true etc. This is the main location 
of end user customization. 

3. By data manipulation in the controller connected
to each component. This data can be in every form,
but should focus on translating other input to view 
data. This file can contain clear-text-classes. 

Example: 
```php
if($isDisabled) { 
    $this->classList[] = 'disabled'; 
}
```
  
4. If the component library is running inside WordPress. 
There is a additional filter that can be used to externally 
manipulate the data as a last step before output. 
 
Filter (General):
BladeComponentLibrary/Component/Data - Takes $data

Filter (Component specific): 
BladeComponentLibrary/Component/ComponentName/Data - Takes $data
    
Filter class (General):
BladeComponentLibrary/Component/Class - Takes $class

Filter class (Component specific): 
BladeComponentLibrary/Component/ComponentName/Class - Takes $class

Class filters extracts the class variable from $data object. 

## Add a builtin component
The most efficient and proposed way of adding a compning is by a PR to this package. It will then be available for everyone to be used. A internal component requires three different files. 

- View 
- Controller
- Configuration

### The view 
The view sould be as simple as possible, in most cases just a few if-statements. For more advanced solution, please consider to use directive components as childs to a larger component. 

**Example:** 

```php
<a class="{{ $class }}" target="{{ $target }}" href="{{ $href or '#' }}">{{ $slot or $text }}</a>
```

### The controller 
The controller should handle all logic associated with a component. This file soule purpose is to remove any logic from the view. 

**Example:** 

```php
namespace BladeComponentLibrary\Component\Button;

class Button extends \BladeComponentLibrary\Component\BaseController 
{
    public function init() {
        $this->data['foo'] = "bar"; 
    }
}
```

### The configuration 
A simple configuration of the slug for the component (used as directive & component name). The default parameters and the view name (defaults to the slug name). The configuration should be written in a valid json format. This file must contain the keys "slug", "default" (default parameters) and "view". 

**Example:** 

```json
{
    "slug":"button",
    "default":{
       "isPrimary":true,
       "isDisabled":false,
       "isOutlined":true,
       "label":"Button text",
       "href":"https:\/\/google.se",
       "target":"_self"
    },
    "view":"button.blade.php"
 }
```

## WordPress Compability
Each component will get their respective WordPress filter registered if WordPress core is included before this library. We simply look for the built-in functions called apply_fitlers. The filter will be named as their respective folder location. 

For example; Card component located in "./src/Component/Card" will get the filter "BladeComponentLibrary/Component/Card/Data" applied before render. The last part of the slug "Data" can be changed to "Class" to just filter the sub array "classes" of the data object. 

A specific filter for each key in the data object will also be created. Fir instance if the data object includes the key 'foo' a filter will be created like this: BladeComponentLibrary/Component/Card/Foo. This will not include the key "data" as it's reserved by above filter.

A generic filter will also be called for the data object called "BladeComponentLibrary/Component/Data". This has the side effect of reserving the data namespace. Therefore you cannot create a component called Data.  

## View variables

All component views will be allocated with some basic parameters. These are listed below. Everything else added to the $data array will automatically be added as a $var named with the key value.

| Variable       | Description                                    |
|----------------|------------------------------------------------|
| $class         | An array of classes that wraps the component.  |
| $compiledClass | An string of classes that wraps the component. |
| $baseClass     | A string of first class assigned.              |
| $attribute     | A string of attributes                         |

## Built With 
- Laravel Blade 5.*

## Dependencies
- PHP 7.0

## Releases

https://github.com/helsingborg-stad/blade-component-library/releases

## Authors

- Sebastian Thulin 
- Johan Silvergrund
- Jonatan Hanson 
- Nikolas Ramstedt
- Eric Rosenborg
- Alexander Boman Skoug

## License 

This project is licensed under the MIT License - see the LICENSE file for details
