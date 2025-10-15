Modularity
==========

Modular component system plugin for WordPress. Drag and drop the bundled modules or your custom modules to your page layout.

## Download plugin.
To download a complete out of the box working plugin without the need to compile or fetch dependencies.  
Go [here](https://github.com/helsingborg-stad/Modularity/releases) and download the `full-release.zip` from the latest version.  

Creating modules
----------------

To create your very own Modularity module you simply create a plugin with a class that extends our Modularity\Module class.

A module actually is the same as a custom post type. However we've added a few details to enable you to use them as modules.

Use the `$this->register()` method to create a very basic module.

Here's a very basic example module for you:

```php
/*
 * Plugin Name: Modularity Article Module
 * Plugin URI: -
 * Description: Article module for Modularity
 * Version: 1.0
 * Author: Modularity
 */

namespace MyArticleModule;

class Article extends \Modularity\Module
{
    public function __construct()
    {
        $id = 'article';
        $nameSingular = 'Article';
        $namePlural = 'Articles';
        $description = 'Outputs a full article with title and content';
        $supports = array('editor'); // All modules automatically supports title
        $icon = '[BASE-64 encoded svg data-uri]';
        $plugin = '/path/to/include-file.php' // CAn also be an array of paths to include 
        $cacheTTL = 60*60*24 //Time to live for fragment cache (stored in persistent object store redis / memcached). 

        $this->register(
            $id,
            $nameSingular,
            $namePlural,
            $description,
            $supports,
            $icon,
            $plugin,
            $cacheTTL
        );
    }
}

new \MyArticleModule\Article;
```

#### Module templates

You can easily create your own module templates by placing them in: `/wp-content/themes/[my-theme]/templates/module/`.

Name your template file with the following pattern: `modularity-[module-id].php`. You can get your module's id from the Modularity options page.

#### Module boilerplate

You can download our module boilerplate. It will be a good starting point for any custom module that you would like to build.

[Download it here (NOT AVAILABLE YET)](http://www.helsingborg.se)


Action reference
----------------

#### Modularity

> Runs when Modularity core is loaded. Typically used to add custom modules.

*Example:*
```php
add_action('Modularity', function () {
    // Do your thing
});
```

#### Modularity/Module/[MODULE SLUG]/enqueue

> Enqueue js or css only for the add and edit page of the specified module.

*Example:*

```php
add_action('Modularity/Module/mod-article/enqueue', function () {
    // Do your thing
});
```

#### Modularity/Options/Module

> Action to use for adding option fields to modularity options page.
> Use "Modularity/Options/Save" action to handle save of the option field added

*Example:*

```php
add_action('Modularity/Options/Module', function () {
    echo '<input type="text">';
});
```

#### Modularity/save_block

> Action triggered whenever a post or page is created or updated.

*Example:*

```php
add_action('Modularity/save_block', function ($block, $post) {
    // Your code here
});
```
Filter reference
----------------
#### Modularity/Module/TemplateVersion3

> Enable preview of the upcoming version 3 views with BEM formatting. 
> This may be used already when progressing towards BEM. 

*Example:*

```php
add_filter('Modularity/Module/TemplateVersion3', function(){return true;});
```

#### Modularity/Editor/WidthOptions

> Filter module width options

*Params:*
```
$options      The default width options array ('value' => 'label')
```

*Example:*

```php
add_filter('Modularity/Editor/WidthOptions', function ($options) {
    // Do your thing
    return $filteredValue;
});
```

---


#### Modularity/Editor/SidebarIncompability

> Enables the theme to add incompability indicators of specific module to an sidebar area. The user will not be able to drag and drop to unsupported areas. This filter may simplify the theme developers work by ruling out some cases. 

*Params:*
```
$moduleSpecification      The default options for the module post object. 
```

*Example:*

```php
add_filter('Modularity/Editor/WidthOptions', function ($moduleSpecification) {
    
    $moduleSpecification['sidebar_compability'] = array("content-area-top"); 

    return $moduleSpecification;
});
```

---

#### Modularity/Display/BeforeModule

> Filter module sidebar wrapper (before)

*Params:*
```
$beforeModule     The value to filter
$args             Arguments of the sidebar (ex: before_widget)
$moduleType       The module's type
$moduleId         The ID of the module
```

*Example:*
```php
add_filter('Modularity/Display/BeforeModule', function ($beforeModule, $args, $moduleType, $moduleId) {
    // Do your thing
    return $filteredValue;
});
```

---

#### Modularity/Display/AfterModule

> Filter module sidebar wrapper (after)

*Params:*
```
$afterModule      The value to filter
$args             Arguments of the sidebar (ex: before_widget)
$moduleType       The module's type
$moduleId         The ID of the module
```

*Example:*

```php
add_filter('Modularity/Display/AfterModule', function ($afterModule, $args, $moduleType, $moduleId) {
    // Do your thing
    return $filteredValue;
});
```

---

#### Modularity/Module/Container/Sidebars

> Container wrapper: Filter what sidebars that should support a containing wrapper on some modules

*Params:*
```
$sidebars      A array of sidebar id's
```

---

#### Modularity/Module/Container/Modules

> Container wrapper: Filter what modules that should support a containing wrapper

*Params:*
```
$modules      A array of module ids (post-type names)
```

---

#### Modularity/Module/Container/Template

> Container wrapper: Filter the template with html that should be wrapped around each module

*Params:*
```
$markup      A string with markup containing {{module-markup}} replacement key
```

---

#### Modularity/Module/TemplatePath & Modularity/Theme/TemplatePath

> Modify (add/edit) paths where to look for module/theme templates
> Typically used for adding search path's for finding custom modules/theme templates.
> 
> *Attention: Unsetting paths may cause issues displaying modules. Plase do not do this unless you know exacly what you are doing.*

*Params:*
```
$paths      The value to filter
```

*Example:*
```php
add_filter('Modularity/Module/TemplatePath', function ($paths) {
    return $paths;
});

add_filter('Modularity/Theme/TemplatePath', function ($paths) {
    return $paths;
});
```

#### Modularity/Module/Classes

> Modify the list of classes added to a module's main element

*Params:*
```
$classes      The classes (array)
$moduleType   The module type
$sidebarArgs  The sidebar's args
```

*Example:*
```php
add_filter('Modularity/Module/Classes', function ($classes, $moduleType, $sidebarArgs) {
    $classes[] = 'example-class';
    return $classes;
});
```

#### Modularity/Display/Markup

> Module display markup

*Params:*
```
$markup      The markup
$module      The module post
```

*Example:*
```php
add_filter('Modularity/Display/Markup', function ($markup, $module) {
    return $markup;
});
```

#### Modularity/Display/[MODULE SLUG]/Markup

*Params:*
```
$markup      The markup
$module      The module post
```

*Example:*
```php
add_filter('Modularity/Display/Markup', function ($markup, $module) {
    return $markup;
});
```

#### Modularity/CoreTemplatesSearchTemplates

> What template files to look for

*Params:*
```
$templates
```

*Example:*
```php
add_filter('Modularity/CoreTemplatesSearchTemplates', function ($templates) {
    $templates[] = 'my-custom-template';
    return $templates;
});
```

#### Modularity/Module/Posts/Date

> Modify the displayed publish date in Post Modules

*Params:*
```
$date
$postId
$postType
```

*Example:*
```php
add_filter('Modularity/Module/Posts/Date', function ($date, $postId, $postType) {
    return $date;
});
```
#### Modularity/Module/Posts/Slider/Arguments

> Modify the slider arguments in Post Modules

*Params:*
```
$args['slider']['slidesPerPage']
$args['slider']['autoSlide']
$args['slider']['showStepper']
$args['slider']['repeatSlide']
```
*Example:*
```php
add_filter('Modularity/Module/Posts/Slider/Arguments', function ($args) {
    return $args;
});
```

#### Modularity/Editor/ModuleCssScope

> Allow editors to select a unique appeance (provided by a theme etc) for a module. Adds a single class to the module wrapper, to allow scoping of css styles. 

*Params:*
```
$scopes - Previously declared scopes. 
```

*Example:*
```php
add_filter('Modularity/Editor/ModuleCssScope',function($scopes) {
        return array(
            'mod-posts' => array(
                's-buy-card' => __("Make this module sparkle!", 'modularity'),
                's-user-list' => __("A boring user list is what i see", 'modularity')
            )
        );
    });
```

#### Modularity/Display/viewData

*Params:*
```
$viewData - array
```

*Example:*
```php
add_filter('Modularity/Display/viewData',function($data) {
        //do something
        return $data;
    });
```

#### Modularity/Display/{modulePostTypeSlug}/viewData

*Params:*
```
$viewData - array
```

*Example:*
```php
add_filter("Modularity/Display/mod-posts/viewData", function($data) {
        //do something
        return $data;
    });
```

## Module Attributes API
Some module features are available by setting certain attributes on the module's outmost element.

### `data-module-refresh-interval`
Creates an interval on which the module is refreshed via XHR by calling the REST API. This attribute also requires that the `data-module-id` attribute is set on the same element.

The value of the attribute should be the number of seconds on which to run the refresh interval.

Example:
```html
<div data-module-id="123" data-module-refresh-interval="60">
    This content will get refreshed every 60 seconds.
</div>
```

## Rest API
The WordPress REST API is extended with the following endpoints.

### `modularity/v1/modules/{id}`
This endpoint returns the markup for a specific module.

* Method: `GET`
* Params:
    * `id`: The ID of the module to retrieve.
* Response: The html markup of the module.

## Upgrade
In some updates, database migrations might need to be run (will be in the release docs). To run the upgrade action on all sites in a multisite network, run the following command in the document root of your site. 

```
wp site list --field=url --public=1 --archived=0 --deleted=0 --allow-root | xargs -n1 -I % wp modularity upgrade --url=% --allow-root
```

Or for a single site installation: 

```
wp modularity upgrade
```

## Constants

### `MODULARITY_DISABLE_FRAGMENT_CACHE`
Disabling the built-in fragment cache means that each module's output will not be stored as an HTML cache. As a result, every time a visitor reloads the page, each module will have to be rendered again. However, it is important to consider this option only if you do not have any object cache enabled, such as Redis or Memcached. We strongly advise enabling the fragment cache feature in your application to enhance performance and caching efficiency, allowing modules to be rendered more quickly and reducing the need for repetitive rendering upon page reloads.

By defining this as true, cache will be turned off. Default: Undefined. 

If enabled, each cached module will be rendered with a timestamp and cache ID in the following format:
<!-- FGC: [2023-06-27 09:24:01| 3qee8e1n3m90]-->

## Tested with support from BrowserStack
This software is tested with the awesome tools from Browserstack.

<a href="https://browserstack.com"><img src="https://utveckling.helsingborg.se/wp-content/uploads/sites/19/2018/09/browserstack-logo.png"/></a>
