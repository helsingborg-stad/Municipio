# Feature: SchemaData

## Description
This feature adds schema data to your site. Schema data is a type of metadata that helps search engines understand the content of your site. This can help improve the appearance of your site in search results.
Post types can be configured to use a specific type of schema data.

## Requirements
* If OPcache is enabled, set `opcache.save_comments=On`.\
When calculating which types a schema property is allowed to use, we use `ReflectionMethod::getDocComment()` to extraxt all the types from the @var annotation. This does not work when using a cache like like OPcache since this removes comments from files to save storage space.

## Usage
Select which Schema type should be used with each post type on the post list page for the post type in the WordPress admin area.  
This will add the schema data to the post type's archive page and single post pages.

## Adding Schema Data to a post
* First you need to enable this feature, which can be done at the Theme Options admin page.
* Next, go to the post list page for the post type you want to add schema data to.
* In the Schema.org meta box, select the schema type you want to use for the post type.
* When you have selected a schema type, a number of meta fields will appear on the post edit page of the current post type.
* Fill in the fields with the appropriate data.