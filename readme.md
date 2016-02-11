# Municipio 1.0 (for Helsingborg stad)

## Getting started
To get started you'll need to install the required npm packages. To install these components you will need to have Node.js installed on your system.

```
$ cd [THEME-DIR]
$ npm install
$ bower install
```

## Coding standards
For PHP, use PSR-2 and PSR-4 where applicable.

## Gulp
We use Gulp to compile, concatenate and minify SASS and JavaScript.
The compiling of SASS will also automatically add vendor-prefixes where needed.

To compile both js and sass and start the "watch" task run the following command from the theme directory:
```
$ gulp
```

## Filters

#### Municipio/header_grid_size
Applied to classes string for header sizes.

- ```@param string $classes``` - 

```php
apply_filters('Municipio/header_grid_size', $classes);
```


#### Municipio/mobile_menu_breakpoint
Applied to classes string for mobile hamburger menu breakpoint. 

- ```@param string $classes``` - The default site name

```php
apply_filters('Municipio/mobile_menu_breakpoint', $classes);
```


#### Municipio/logotype_text
Applied to the text that displays as the logo when now logotype image is uploaded in theme options.

- ```@param string $title``` - The default site name

```php
apply_filters('Municipio/logotype_text', $title);
```

#### Municipio/logotype_class
Applied to the logotype class attirbute

- ```@param array $classes``` - Default class(es)

```php
apply_filters('Municipio/logotype_class', $classes);
```

#### Municipio/logotype_tooltip
Applied to the logotype class attirbute

- ```@param string $tooltip``` - Default tooltip text

```php
apply_filters('Municipio/logotype_tooltip', $tooltip);
```

#### Municipio/blade/data
Applied to the blade template data. Can be used to send data to a Blade view.

- ```@param array $data``` - Dafault data

```php
apply_filters('Municipio/blade/data', $data);
```

#### Municipio/blade/template_types
Applied to the list of Blade template types.

- ```@param array $types``` - Dafault Blade template types

```php
apply_filters('Municipio/blade/template_types', $types);
```
