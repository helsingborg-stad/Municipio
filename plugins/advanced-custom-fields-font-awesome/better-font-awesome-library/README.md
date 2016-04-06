Better Font Awesome Library
===========================

*The easiest way to integrate Font Awesome into your WordPress project.*

## Table of contents ##
1. [Introduction](https://github.com/MickeyKay/better-font-awesome-library#introduction)
1. [Features](https://github.com/MickeyKay/better-font-awesome-library#features)
1. [Installation](https://github.com/MickeyKay/better-font-awesome-library#installation)
1. [Usage](https://github.com/MickeyKay/better-font-awesome-library#usage)
1. [Initialization Parameters](https://github.com/MickeyKay/better-font-awesome-library#initialization-parameters-args)
1. [Shortcode](https://github.com/MickeyKay/better-font-awesome-library#shortcode)
1. [The Better Font Awesome Library Object](https://github.com/MickeyKay/better-font-awesome-library#the-better-font-awesome-library-object)
1. [Filters](https://github.com/MickeyKay/better-font-awesome-library#filters)
1. [To Do](https://github.com/MickeyKay/better-font-awesome-library#to-do)
1. [Credits](https://github.com/MickeyKay/better-font-awesome-library#credits)

## Introduction ##
The Better Font Awesome Library allows you to automatically integrate the latest available version of [Font Awesome](http://fontawesome.io/) into your WordPress project, along with accompanying CSS, shortcode, and TinyMCE icon shortcode generator. Furthermore, it generates all the data you need to create new functionality of your own.

## Features ##
* Automatically fetches the most recent available version of Font Awesome, meaning you no longer need to manually update the version included in your theme/plugin.
* Generates an easy-to-use [PHP object](#the-better-font-awesome-library-object) that contains all relevant info for the version of Font Awesome you're using, including: version, stylesheet URL, array of available icons, and prefix used (`icon` or `fa`).
* CDN speeds - Font Awesome CSS is pulled from the super-fast and reliable [jsDelivr CDN](http://www.jsdelivr.com/#!fontawesome).
* Includes a TinyMCE drop-down shortcode generator.
* Includes a local copy of Font Awesome to use as a fallback in case the remote fetch fails (or you can specify your own with the [`bfa_fallback_directory_path`](https://github.com/MickeyKay/better-font-awesome-library#bfa_fallback_directory_path) filter).
* Utilizes transients to optimize for speed and performance.

## Installation ##
The Better Font Awesome Library contains a [Git submodule](http://git-scm.com/book/en/Git-Tools-Submodules) which will require you to recursively clone this repo:
```
git clone --recursive https://github.com/MickeyKay/better-font-awesome-library.git
```

Alternately, if you've already cloned the repo and need to add the submodules, you can run the following command:
```
// Initialize and update all submodules.
git submodule update --init --recursive
```

## Usage ##
1. Copy the /better-font-awesome-library folder into your project.

2. Add the following code to your main plugin file or your theme's functions.php file.
   ```
   	// Include the main library file. Make sure to modify the path to match your directory structure.
	require_once ( dirname( __FILE__ ) . '/better-font-awesome-library/better-font-awesome-library.php' );

	add_action( 'init', 'my_prefix_load_bfa' );
	/**	
	 * Initialize the Better Font Awesome Library.
	 *
	 * (see usage notes below on proper hook priority)
	 */
	function my_prefix_load_bfa() {

		// Set the library initialization args (defaults shown).
		$args = array(
				'version' => 'latest',
				'minified' => true,
				'remove_existing_fa' => false,
				'load_styles'             => true,
				'load_admin_styles'       => true,
				'load_shortcode'          => true,
				'load_tinymce_plugin'     => true,
		);
		
		// Initialize the Better Font Awesome Library.
		Better_Font_Awesome_Library::get_instance( $args );

	}
```

3. If desired, use the [Better Font Awesome Library object](#the-better-font-awesome-library-object) to manually include Font Awesome CSS, output lists of available icons, create your own shortcodes, and much more.

#### Usage Notes ####
The Better Font Awesome Library is designed to work in conjunction with the [Better Font Awesome](https://wordpress.org/plugins/better-font-awesome/) WordPress plugin. The plugin initializes this library (with its initialization args) on the `init` hook, priority `5`. When using the Better Font Awesome Library in your project, you have two options:

1. Initialize later, to ensure that any Better Font Awesome plugin settings override yours (this is the default behavior, shown above by initializing the library on the `init` hook with default priority `10`.
1. Initialize earlier, to "take over" and prevent Better Font Awesome settings from having an effect.

## Initialization Parameters ($args) ##
The following arguments can be used to initialize the library using `Better_Font_Awesome_Library::get_instance( $args )`:

#### $args['version'] ####
(string) Which version of Font Awesome you want to use.
* `'latest'` (default) - always use the latest available version.
* `'3.2.1'` - any existing Font Awesome version number.

#### $args['minified'] ####
(boolean) Use minified Font Awesome CSS.
* `true` (default) - uses minifed CSS.
* `false` - uses unminified CSS.

#### $args['remove_existing_fa'] ####
(boolean) Attempts to remove existing Font Awesome styles and shortcodes. This can be useful to prevent conflicts with other themes/plugins, but is no guarantee.
* `true`
* `false` (default)

#### $args['load_styles'] ####
(boolean) Automatically loads Font Awesome CSS on the **front-end** of your site using `wp_enqueue_scripts()`.
* `true` (default)
* `false` - use this if you don't want to load the Font Awesome CSS on the front-end, or wish to do it yourself.

#### $args['load_admin_styles'] ####
(boolean) Automatically loads Font Awesome CSS on the **admin** of your site using `admin_enqueue_scripts()`.
* `true` (default)
* `false` - use this if you don't want to load the Font Awesome CSS in the admin, or wish to do it yourself.

#### $args['load_shortcode'] ####
(boolean) Loads the included `[icon]` [shortcode](https://github.com/MickeyKay/better-font-awesome-library#shortcode).
* `true` (default)
* `false`

#### load_tinymce_plugin ####
(boolean) Loads a TinyMCE drop-down list of available icons (based on the active Font Awesome version), which generates an `[icon]` shortcode.
* `true` (default)
* `false`

## Shortcode ##
If either the `$args['load_shortcode']` or `$args['load_tinymce_plugin']` initialization arg is set to `true`, then the Better Font Awesome Library will include an icon shortcode that can be used as follows:
```
[icon name="star" class="2x spin" unprefixed_class="my-custom-class"]
```

**name**  
The unprefixed icon name (e.g. star). The version-specific prefix will be automatically prepended.

**class**  
Unprefixed [Font Awesome icon classes](http://fortawesome.github.io/Font-Awesome/examples/). The version-specific prefix will be automatically prepended to each class.

**unprefixed_class**  
Any additional classes that you wish to remain unprefixed (e.g. my-custom-class).

#### Shortcode Output ####
The example shortcode above would output the following depending on which version of Font Awesome you've selected:

**Version 4+**
```
<i class="fa fa-star fa-2x fa-spin my-custom-class"></i>
```
**Version 3**
```
<i class="icon icon-star icon-2x icon-spin my-custom-class"></i>
```

## The Better Font Awesome Library Object ##
The Better Font Awesome Library object can be accessed with the following code:  
`Better_Font_Awesome_Library::get_instance();`

The object has the following public methods:
#### get_version() ####
(string) Returns the active version of Font Awesome being used.

#### get_stylesheet_url() ####
(string) Returns the active Font Awesome stylesheet URL.

#### get_icons() ####
(array) Returns an associative array of icon hex values (index, e.g. \f000) and unprefixed icon names (values, e.g. rocket) for all available icons in the active Font Awesome version.

#### get_prefix() ####
(string) Returns the version-dependent prefix ('fa' or 'icon') that is used in the icons' CSS classes.

#### get_api_data() ####
(object) Returns version data for the remote jsDelivr CDN (uses [jsDelivr API](https://github.com/jsdelivr/api)). Includes all available versions and latest version.

#### get_errors() ####
(array) Returns all library errors, including API and CDN fetch failures.

#### Example: ####
```
// Initialize the library with custom args.
Better_Font_Awesome_Library::get_instance( $args );

// Get the active Better Font Awesome Library Object.
$my_bfa = Better_Font_Awesome_Library::get_instance();

// Get info on the Better Font Awesome Library object.
$version = $my_bfa->get_version();
$stylesheet_url = $my_bfa->get_stylesheet_url();
$prefix = $my_bfa->get_prefix();
$icons = $my_bfa->get_icons();

// Output all available icons.
foreach ( $icons as $icon ) {
	echo $icon . '<br />';
}
```

## Filters ##
The Better Font Awesome Library applies the following filters:

#### bfa_init_args ####
Applied to the initialization arguments after they have been parsed with default args, but before they are used to fetch any Font Awesome data.

**Parameters**

* `$init_args` (array)

#### bfa_wp_remote_get_args ####
Applied to arguments passed to all `wp_remote_get()` calls (useful for adjusting the timeout if needed).

**Parameters**

* `$wp_remote_get_args` (array)

#### bfa_fallback_directory_path ####
Applied to the fallback directory path before setting up any fallback CSS info. Can be used to specify an alternate fallback directory to replace the default fallback directory.

*The path must be to a local, non-remote, directory.*

**Parameters**

* `$path` (string)

#### bfa_api_transient_expiration ####
Applied to the API (version information) transient [expiration](http://codex.wordpress.org/Transients_API#Using_Transients). Can be used to increase/decrease the expiration as desired.

**Parameters**

* `$api_expiration` (int)

#### bfa_css_transient_expiration ####
Applied to the CSS stylesheet data transient [expiration](http://codex.wordpress.org/Transients_API#Using_Transients). Can be used to increase/decrease the expiration as desired.

**Parameters**

* `$css_expiration` (int)

#### bfa_icon_list ####
Applied to the icon array after it has been generated from the Font Awesome stylesheet, and before it is assigned to the Better Font Awesome Library object's `$icons` property.

**Parameters**

* `$icons` (array)

#### bfa_prefix ####
Applied to the Font Awesome prefix ('fa' or 'icon') before it is assigned to the Better Font Awesome Library object's `$prefix` property.

**Parameters**

* `$prefix` (string)

#### bfa_icon_class ####
Applied to the classes that are output on each icon's `<i>` element.

**Parameters**

* `$class` (string)

#### bfa_icon ####
Applied to the entire `<i>` element that is output for each icon.

**Parameters**

* `$output` (string)

#### bfa_force_fallback ####
Applied to the boolean that determines whether or not to force the included fallback version of Font Awesome to load. This can be useful if you're having trouble with delays or timeouts.

**Parameters**

* `$force_fallback` (false)

#### bfa_show_errors ####
Applied to the boolean that determines whether or not to suppress all Font Awesome warnings that normally display in the admin.

**Parameters**

* `$show_errors` (true)


## To Do ##
Ideas? File an issue or add a pull request!
* Add README section on manually updating the fallback version.
* Remove existing FA? - move to later hook so that it works for styles enqueued via shortcode (= wp_footer basically)

## Credits ##
Special thanks to the following folks and their plugins for inspiration and support:
* [Font Awesome Icons](http://wordpress.org/plugins/font-awesome/ "Font Awesome Icons") by [Rachel Baker](http://rachelbaker.me/ "Rachel Baker")
* [Font Awesome More Icons](https://wordpress.org/plugins/font-awesome-more-icons/ "Font Awesome More Icons") by [Web Guys](http://webguysaz.com/ "Web Guys")
* [Font Awesome Shortcodes](https://wordpress.org/plugins/font-awesome-shortcodes/) by [FoolsRun](https://profiles.wordpress.org/foolsrun/ "FoolsRun")
* Dmitriy Akulov and the awesome folks at [jsDelivr](http://www.jsdelivr.com/)

And many thanks to the following folks who helped with testing and QA:
* [Jeffrey Dubinksy](http://vanishingforests.org/)
* [Neil Gee](https://twitter.com/_neilgee)
* [Michael Beil](https://twitter.com/MichaelBeil)
* [Rob Neue](https://twitter.com/rob_neu)
* [Gary Jones](https://twitter.com/GaryJ)
* [Jan Hoek](https://twitter.com/JanHoekdotCom)
