# Municipio 3

## Download theme.
To download a complete out of the box working plugin without the need to compile or fetch dependencies.
Go [here](https://github.com/helsingborg-stad/Municipio/releases) and download the `full-release.zip` from the latest version.

## Getting started
It is recommended to install the bundled version of municipio trough our build management repository. [This can be found here.](https://github.com/municipio-se/municipio-deployment)

To get started you'll need to install the required npm and composer packages. To install these components you will need to have Node.js and Composer installed on your system.

```
$ cd [THEME-DIR]
$ php build.php
```

## Dependencies
Municipio requires [ACF PRO](https://www.advancedcustomfields.com/pro/).

## Coding standards
For PHP, use PSR-2 and PSR-4 where applicable.

## Gulp
You can use Gulp to compile, concatenate and minify SASS and JavaScript.
The compiling of SASS will also automatically add vendor-prefixes where needed.

To compile both js and sass and start the "watch" task run the following command from the theme directory:
```
$ gulp
```

## We added support for webpack
Webpack is a bit faster than gulp. So if you prefer to use webpack instead of gulp, just run the following command:
```
$ npm run watch
```
or just for a single build:
```
$ npm run build
```

## NPM scripts

* **watch**: Run webpack in watch mode. To be used when working on client side code like scss,js,ts...
* **build:dev**: Build assets with the development flag.
* **build**: Build assets for production.
* **i18n:update**: Updates languages/municipio.pot and languages/sv_SE.po from source files. Run this when translatable strings have been added/updated. **Requires wp-cli**.
* **i18n:build**: Updates languages/municipio.mo .po file. Run this when changes have been made to the corresponding .po file. **Requires wp-cli**.

## Composer
You can install composer dependencies with.
```
$ composer install
```

### Composer scripts
```bash
composer run test # Run phpunit tests
```
```bash
composer run test:coverage # Run phpunit tests with code coverage
```
```bash
composer run lint # Run phpcs
```
```bash
composer run lint:changed # Run phpcs on changed files (Compares to main branch).
```
```bash
composer run fix # Run phpcbf to apply fixes to code.
```
```bash
composer run fix:changed # Run phpcbf to apply fixes to changed files (Compares to main branch).
```

## Constants

### Block author pages
Author pages is blocked by default. To "unblock" add the following constant to wp-config (or other suitable place).

```
define('MUNICIPIO_BLOCK_AUTHOR_PAGES', false);
```

### Load styleguide from a different host
Constants for setting the base URI to the styleguide. Useful for third-party sites.

```
define('MUNICIPIO_STYLEGUIDE_URI', '//example.com/style/guide');
```

### Load specific version of styleguide
Constants that lock version of the styleguide. Comes in handy when you want to enshure maximum stability of a site.

```
define('STYLEGUIDE_VERSION', 1.0.32);
```

### Load specific developement version of styleguide
Constant that load local verrsion of the styleguide.

```
define('DEV_MODE', true);
```

### CSS class in BEMIT style for theme name
Constant that contains the current theme name in BEM format. Usable when you wnat to connect component styling directly to the theme.You cannot change this.

```
MUNICIPIO_BEM_THEME_NAME
```

## Actions

### Municipio/blog/post_info

Blog post info header (single)

- ```@param object $post``` - The post object

```php
do_action('Municipio/author_display/name', $post);
```

### Municipio/share_post/recipients

Do action on sharing post by email, e.g. send a notification

- ```@param object $user``` - User object for the sender
- ```@param array $recipients``` - List with e-mail addresses

```php
do_action('Municipio/share_post/recipients', $user, $recipients);
```

### Municipio/comment/save_like

Do action on comment like

- ```@param object $comment``` - Comment object
- ```@param int $userId``` - Current user ID
- ```@param bool $create``` - True if a new like is created. False if it's removed

```php
do_action('Municipio/comment/save_like', $comment, $userId, $create);
```

### Municipio/post/page/edit

Do action on post edit in admin. Runs after the current_screen hook.
- ```@param int $postId``` - The post ID
- ```@param string $postType``` - The post type
```php
do_action('Municipio/post/page/edit', $postId, $postType);
```

## Filters

### Municipio/Template/viewData
- ```@param array $viewData``` - The view data that is passed to filter.
```php
apply_filters('Municipio/Template/viewData', $viewData);
```
**Modifiers**

- ```@param array $viewData``` - The view data that is passed to filter.
- ```@param string $postType``` - The post type slug.
- ```@param string $template``` - The archive template name.

```php
apply_filters('Municipio/Template/single/viewData', $viewData, $postType);
apply_filters('Municipio/Template/archive/viewData', $viewData, $postType, $template);
apply_filters("Municipio/Template/{$postType}/viewData", $viewData);
apply_filters("Municipio/Template/{$postType}/single/viewData", $viewData);
apply_filters("Municipio/Template/{$postType}/archive/viewData", $viewData, $template);
```

**Deprecated:**

- `Municipio/controller/base/view_data`
- `Municipio/blade/data`
- `Municipio/Controller/Archive/Data`
- `Municipio/viewData`

### Municipio/theme/key
Filters the theme/styleguide asset key.

- ```@param string $key``` - The key of the styleguide theme

```php
apply_filters('Municipio/theme/key', $key);
```

### Municipio/author_display/name
Set the name of the author display

- ```@param string $name``` - Default name
- ```@param string $userId``` - The ID of the user

```php
apply_filters('Municipio/author_display/name', $name, $userId);
```

### Municipio/author_display/title
Set the title label for the author name display

- ```@param string $title``` - Default title

```php
apply_filters('Municipio/author_display/title', $title);
```

### Municipio/ajax_url_in_head
Set the ajax_url in the <head>

- ```@param string $ajax_url``` - Default ajax url

```php
apply_filters('Municipio/ajax_url_in_head', $ajax_url);
```

### Municipio/favicon_sizes
Add sizes to theme options for favicon

- ```@param array $sizes``` - Default favicon sizes

```php
apply_filters('Municipio/favicon_sizes', $sizes);
```

### Municipio/favicon_tag
Add sizes to theme options for favicon

- ```@param string $tag``` - The HTML tag(s)
- ```@param array $icon``` - The icon data

```php
apply_filters('Municipio/favicon_tag', $tag, $icon);
```

### Municipio/header_grid_size
Applied to classes string for header sizes.

- ```@param string $classes``` -

```php
apply_filters('Municipio/header_grid_size', $classes);
```


### Municipio/mobile_menu_breakpoint
Applied to classes string for mobile hamburger menu breakpoint.

- ```@param string $classes``` - The default site name

```php
apply_filters('Municipio/mobile_menu_breakpoint', $classes);
```


### Municipio/logotype_text
Applied to the text that displays as the logo when now logotype image is uploaded in theme options.

- ```@param string $title``` - The default site name

```php
apply_filters('Municipio/logotype_text', $title);
```

### Municipio/logotype_class
Applied to the logotype class attirbute

- ```@param array $classes``` - Default class(es)

```php
apply_filters('Municipio/logotype_class', $classes);
```

### Municipio/logotype_tooltip
Applied to the logotype class attirbute

- ```@param string $tooltip``` - Default tooltip text

```php
apply_filters('Municipio/logotype_tooltip', $tooltip);
```

### Municipio/blade/template_types
Applied to the list of Blade template types.

- ```@param array $types``` - Dafault Blade template types

```php
apply_filters('Municipio/blade/template_types', $types);
```

### Municipio/search_result/…
Multiple filters applied to the contents of a search result

- ```@param string $var``` - The content of the variable
- ```@param object $post``` - Post object

```php
apply_filters('Municipio/search_result/date', $date, $post);
apply_filters('Municipio/search_result/title', $title, $post);
apply_filters('Municipio/search_result/excerpt', $excerpt, $post);
apply_filters('Municipio/search_result/permalink_url', $permalink_url, $post);
apply_filters('Municipio/search_result/permalink_text', $permalink_text, $post);
```

### Municipio/search_form/…
Filters applied to the search form

- ```@param string $var``` - The content of the variable

```php
apply_filters('Municipio/search_form/action', $url);
```

### Municipio/archive/sort_keys
Modify the avaiable sorting keys for archives

- ```@param array $keys``` - The keys
- ```@param string $postType``` - The post type

```php
apply_filters('Municipio/archive/sort_keys', $keys, $postType);
```

### Municipio/archive/date_filter
Modify the date filter WHERE clause

- ```@param string $where``` - The sql WHERE clause
- ```@param string $from``` - The "from" date from querystring
- ```@param string $to``` - The "to" date from querystring

```php
apply_filters('Municipio/archive/date_filter', $where, $from, $to);
```

### Municipio/Breadcrumbs
Show/hide (true/false) breadcrumbs

- ```@param boolean $bool```- True or false (show or hide)

```php
apply_filters('Municipio/Breadcrumbs', $bool, get_queried_object())
```

### Municipio/Breadcrumbs/Items
Filter the items/links in the breadcrumb

- ```@param array $items``` - The breadcrumb items

```php
apply_filters('Municipio/Breadcrumbs/Items', $items, get_queried_object());
```

### Municipio/admin/editor_stylesheet
Change custom editor stylesheet

- ```@param string $url``` - The stylesheet url

```php
apply_filters('Municipio/admin/editor_stylesheet', $url);
```

### Municipio/oembed/should_filter_markup
Decide if oembed markup should be filtered to HbgPrime video player (youtube and vimeo) or not.

- ```@param string $url``` - The resource url
- ```@param int $postId``` - Id of the current post

```php
apply_filters('Municipio/oembed/should_filter_markup', true, $url, $postId);
```


### Municipio/Menu/Vertical/EnabledSidebars
Dictates what sidebars that sould be active on the current page to show the vertical menu. Simple array containing the sidebar id's.

- ```@param array $sidebars``` - An flat array with sidebar id's.

```php
apply_filters('Municipio/Menu/Vertical/EnabledSidebars', $sidebars);
```

### Municipio/archive/tax_query
Additional taxonomy queries.

- ```@param array $taxQuery``` - Holds the taxonomy query.
- ```@param object $query``` - Current query object.

```php
apply_filters('Municipio/archive/tax_query', $taxQuery, $query);
```

### Municipio/taxonomy/tag_style
Adds custom style to taxonomy labels.

- ```@param string $style``` - Custom CSS.
- ```@param string $term``` - The term.
- ```@param string $taxonomy``` - Taxonomy.

```php
apply_filters('Municipio/taxonomy/tag_style', $style, $term, $taxonomy);
```

### Municipio/Menu/Vertical/Items
Items that should be visible in the vertical navigation menus. Represented as dots with hover-labels.

- ```@param array $items``` - An array with items representing links.

```php
apply_filters('Municipio/Menu/Vertical/EnabledSidebars', array(array('title' => 'Page section title', 'link' => '#anchorlink'));
```

### Municipio/ContentType/schemaParams

Modify the schema parameters for a content type. This filter is applied to the array of schema parameters associated with a content type, allowing for the extension or modification of the structured data (schema.org) attributes for that content type.

- `@param array $schemaParams` - The default schema parameters for the content type.
- `@param string $contentTypeKey` - The key identifying the content type.

```php
add_filter('Municipio/ContentType/schemaParams', function($schemaParams, $contentTypeKey) {
    if ($contentTypeKey === 'yourContentTypeKey') {
        $schemaParams['newParameter'] = [
            'schemaType' => 'Text',
            'value' => 'Custom Value'
        ];
    }

    return $schemaParams;
}, 10, 2);
```
### Municipio/Controller/SingularContentType/listing

Modifies the listing array for the Place content type by appending location-related list items. This allows for the inclusion of additional information such as street name or a Google Maps link directly within the listing display.

- `@param array $listing` - The existing listing array that will be modified.
- `@param array $fields` - The fields associated with the post, used to extract location information.

```php
add_filter('Municipio/Controller/SingularContentType/listing', function($listing, $fields) {
    // Example modification to the listing array
    if (!empty($fields['custom_field'])) {
        $listing['custom_field'] = $fields['custom_field'];
    }

    return $listing;
}, 10, 2);
```
## REST routes
The following REST API routes are available from the theme.

### ```[GET] municipio/v1/view/render/{view}```
Returns html for the
* ```@param string $view``` - Path to the blade view to be rendered. Appended to the route.
* ```@param object $data``` - Data to be passed to the view.

Example usage
```typescript
fetch('/wp-json/municipio/v1/view/render/partials/preloader')
    .then((response) => console.log(response))
```

## Dev mode
To load assets from local styleguide. Set contant DEV_MODE to "true"

```php
define('DEV_MODE', true);
```

## Theme fonts
Municipio is integrated with google web-fonts. It enables smart loading of fonts preventing invisible fonts using Google & Adobe webfont loader.

```php
define('WEB_FONT', 'Roboto'); //The google fonts name (without weights)
define('WEB_FONT_REMOTE', true); //Load font kit from cdn
```
# Version 2.0
The goal of version 2.0 is to restructure the theme frontend and move towards the BEM (IT) standard for markup. More filters will be added in a automatic manner, mutch like ACF doe's it. These will for now, be documented below.

## Deprecated functionality (notice phase)
Version 2.0 will introduce some warnings aboute the removal of some prevoius functionality. According to plan, this functionality will be actually be removed in version 3.0. Functions that will be removed in 3.0 are.

- Gravitiforms optimizations
- Honeypot functionality for comments (this will be moved to separate plugin). Will also include google recaptcha.
- Contact widget (replacement avabile in modularity)
- RichText Widget (replacement embedded in core)
- PostType & Taxonomy creator (move to plugin)
- Upload filters (move to plugin)

## Filters

### Deprecated filters
- HbgBlade/data replaced with Municipio/viewData
- Municipio/ajax_url_in_head replaced with Municipio/ajaxUrl
- Modularity/CoreTemplatesSearchPaths

### Blade view filter
All variables sent (created) in a controller will automatically go trough a filter named with the variable key.

```php
apply_filters('Municipio/{{KEY}}', $var);
```

## Global view filter
You may prefer to get a full array of everything sent to a view. After the filter above has run, a global filter will be applied. This replaces the old  filter.

```php
apply_filters('Municipio/viewData', $var);
```
## Constants
MUNICIPIO_FRAGMENT_CACHE - Set to false to remove fragment cache.

## Theme view structure

```
bem-views
│   [Main folder for  theme views, containing WordPress templates like page.blade.php]
│
└───components
│   │   [Components for the theme like card.blade.php]
│   │
└───partials
│   │   [Big chunks that are reused in templates footer.blade.php]
│   │
└───templates
    │   [General templates that are included in main WordPress views like master.blade.php]
│   │
└───utilities
    │   [Small pieces used by components like button.blade.php]
│   │
└───widgets
    │   [Widgetized components]
    │

```

## PHP Tests
Municipio uses [PHPUnit](https://phpunit.de/) for unit testing. For mocking and stubbing we use [WP_Mock](https://wp-mock.gitbook.io/). This means that you can use WP_Mock, [Mockery](https://github.com/mockery/mockery)(since this is a wrapper for WP_Mock) and PHPUnit_MockObject for mocking and stubbing.

### PHPUnit Tests file structure
All tests are stored in the `tests/phpunit/tests` folder. The file structure should mirror the file structure of the theme. The file name should be the same as the file you want to test. For example, if you want to test the file `src/Controller/Base.php` you should create the file `tests/phpunit/tests/Controller/Base.php`. To avoid having too large test files, you can instead create a folder with the same name as the file you want to test and put the test files inside. Please note that for separating files by which class function you are testing, you should name the file e.g. `Base.functionName.php`.

### Running PHPUnit tests
Run `composer test` in the terminal.

### Running PHPUnit tests with code coverage
Run `composer test:coverage` in the terminal. This will generate a code coverage report in the `tests/phpunit/.coverage` folder.

## Image compression
Municipio supports image compression with shortpixel. This will enque a cronjob with a slight delay to compress newly uploaded images. Simpley define SHORTPIXEL_API_KEY constant in your config file and that's it!

Compression level will be medium/glossy for high quality photos.

## Tested with support from BrowserStack
This software is tested with the awesome tools from Browserstack.

<a href="https://browserstack.com"><img src="https://utveckling.helsingborg.se/wp-content/uploads/sites/19/2018/09/browserstack-logo.png"/></a>
