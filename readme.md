# Municipio 1.0 (for Helsingborg stad)

## Getting started
To get started you'll need to install node and bower components. To install these components you will need to have Node.js installed on your system.

```
$ cd [THEME-DIR]
$ npm install
$ bower install
```

## Coding standards
For PHP, use PSR-2 and PSR-4 where applicable.

## Dependencies and components
We manage our dependencies and components with npm and bower. Please check the `package.json` file to see Node dependencies and check the `bower.json`to see the Bower components.

## Gulp
We use Gulp to compile, concatenate and minify SASS and JavaScript.
The compiling of SASS will also automatically add vendor-prefixes where needed.

To compile both js and sass and start the "watch" mode in one command, run the following command from the theme directory:
```
$ gulp
```

#### Available Gulp tasks

All these commands should be run from the theme directory with:

```
$ gulp [TASK]
```

* `jquery-core`     Fetch and compile latest jQuery release from bower_components
* `jquery-ui`       Fetch and compile latest jQuery-UI release from bower_components
* `jquery`          Runs both `jquery-core` and `jquery-ui`
* `sass-dist`       Compiles SASS
* `sass-admin-dist` Compiles admin SASS
* `scripts-dist`    Runs all scripts-* tasks at once
* `scripts-dev`     Compiles JS inside the assets/src/js/dev directory
* `scripts-search`  Compiles JS inside the assets/src/js/search directory
* `scripts-event`   Compiles JS inside the assets/src/js/event directory
* `scripts-alarm`   Compiles JS inside the assets/src/js/alarm directory
* `scripts-admin`   Compiles JS inside the assets/src/js/admin directory
* `scripts-copy`    Copies given bower_components to the assets/js/dist directory
* `watch`           Watches for changes and compiles in assets/src/js and assets/src/css
* `default`         Compiles everything

## Widget areas
