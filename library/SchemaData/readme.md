# Feature: SchemaData

## Description
This feature adds schema data to your site. Schema data is a type of metadata that helps search engines understand the content of your site. This can help improve the appearance of your site in search results.
Post types can be configured to use a specific type of schema data.

## Usage
Select which Schema type should be used with each post type on the post list page for the post type in the WordPress admin area.  
This will add the schema data to the post type's archive page and single post pages.

## How is schema properties added to the post type?
All metadata belonging to the post will be scanned and added to the schema data if it has a format that is supported by the schema type and the property sanitizers in place.  
The name of the meta key needs to be the same as the schema property name, alternatively the meta key can be prefixed with `schema_` to be included in the schema data.

## Note
If no schema type is selected for a post type, the default schema type (Thing) will be used.
For more information on Schema data and the types available, see the [Schema.org website](https://schema.org/).