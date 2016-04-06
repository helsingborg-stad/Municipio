=== Advanced Custom Fields: Font Awesome ===
Contributors: mattkeys
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UTNU7YJG2KVPJ
Tags: Advanced Custom Fields, ACF, Font Awesome, FontAwesome
Requires at least: 3.5
Tested up to: 4.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a new 'Font Awesome Icon' field to the popular Advanced Custom Fields plugin.

== Description ==

Add a [Font Awesome](http://fontawesome.io/) icon field type to Advanced Custom Fields.

* Optionally set a default icon
* Returns Icon Element, Icon Class, Icon Unicode, or an Object including the element, class, and unicode value
* Optionally enqueues Font Awesome in footer
* Integrates with the [Better Font Awesome Library](https://github.com/MickeyKay/better-font-awesome-library) by [Mickey Kay](http://www.mickeykaycreative.com) to automatically use the latest version of the Font Awesome icons

Note: It is recommended to let this plugin enqueue the latest version of Font Awesome on your front-end; or include the latest version by some other means; so that available icons in the admin area will be displayed properly on your sites front-end.

= Compatibility =

This ACF field type is compatible with:
* ACF 5
* ACF 4

== Installation ==

1. Copy the `advanced-custom-fields-font-awesome` folder into your `wp-content/plugins` folder
2. Activate the Font Awesome plugin via the plugins admin page
3. Create a new field via ACF and select the Font Awesome type

== Screenshots ==

1. Set a default icon, and choose how you want icon data to be returned.
2. Searchable list of all icons, including large live preview

== Changelog ==

= 1.7.2 =
* Bugfix PHP Notice when trying to access property of 'null' value

= 1.7.1 =
* Updated Better Font Awesome Library for better compatibility with Better Font Awesome plugin
* Changed ACF 4/5 detection method for better integration with Better Font Awesome plugin
* Bugfix wrong preview icon appearing in ACF custom field creator area

= 1.7 =
* Added ability to select no icon by default
* Better handling of 'null' or 'no selection' items
* Fixed bug where default icon would not display in admin area if 'unicode' return type was selected 

= 1.6.4 =
* Misc JS performance improvements
* Fixed bug where select2 would not initialize on repeater items added mid-rows (using the plus icon at the end of a repeater row)

= 1.6.3 =
* Fixed asset path errors when including this add-on from a theme instead of the plugins directory

= 1.6.2 =
* Rolling back changes from 1.6.1 after a number of bugs were reported. Incompatibility issues with Better Font Awesome have been corrected in that plugins code.

= 1.6.1 =
* Addressing incompatibility issues between this plugin and the Better Font Awesome plugin

= 1.6 =
* Misc fixes to JS to properly target ACF fields in the DOM (based on changes to the ACF structure). This should resolve issues with duplicate, or missing select2 fields when picking font awesome icons.

= 1.5 =
* New Feature: Integrated with the [Better Font Awesome Library](https://github.com/MickeyKay/better-font-awesome-library) to automatically use the latest version of the Font Awesome icons

= 1.4 =
* Updated included FontAwesome to version 4.2

= 1.3 =
* Added support for ACF version 5.x

= 1.2 =
* Added support for new icons in FontAwesome 4.1
* Updated included FontAwesome to version 4.1

= 1.1.2 =
* Fixed overly specific JS selector which was causing font preview icons to not load when used on taxonomy term pages

= 1.1.1 =
* Fixed JS error which was breaking conditional field select boxes

= 1.1.0 =
* Added support for use in repeater fields
* Added support for use in flexible content fields
* Added live icon preview to field creation screen
* Fixed various bugs with Select2 initialization on dynamically added fields

= 1.0.0 =
* Initial Release.

== Upgrade Notice ==

= 1.7.2 =
Bugfix PHP Notice when trying to access property of 'null' value

= 1.7.1 =
Updated Better Font Awesome Library for better compatibility with Better Font Awesome plugin. Minor JS bug fix in admin area

= 1.7 =
Added ability to select no icon by default and better handling of 'null' or 'no selection' items. Fixed bug where default icon would not display in admin area if 'unicode' return type was selected 

= 1.6.4 =
Misc JS performance improvements and fixed bug where select2 would not initialize on repeater items added mid-rows (using the plus icon at the end of a repeater row)

= 1.6.3 =
Fixed asset path errors when including this add-on from a theme instead of the plugins directory

= 1.6.2 =
Rolling back changes from 1.6.1 after a number of bugs were reported. Incompatibility issues with Better Font Awesome have been corrected in that plugins code.

= 1.6.1 =
Addressing incompatibility issues between this plugin and the Better Font Awesome plugin