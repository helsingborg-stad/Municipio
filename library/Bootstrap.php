<?php

define('MUNICIPIO_PATH', dirname(dirname(__FILE__)) . '/');

/**
 * Composer autoloader
 */
if (file_exists(MUNICIPIO_PATH . '/vendor/autoload.php')) {
    require_once MUNICIPIO_PATH . '/vendor/autoload.php';
}

/**
 * Psr4ClassLoader
 */
require_once MUNICIPIO_PATH . 'library/Vendor/Psr4ClassLoader.php';

$loader = new Municipio\Vendor\Psr4ClassLoader();
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'library');
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'source/php/');
$loader->register();

/**
 * Other dependencies
 */
if (file_exists(MUNICIPIO_PATH . 'vendor/bladerunner/bladerunner.php')) {
    new Municipio\Theme\BladerunnerSettings();
    require_once MUNICIPIO_PATH . 'vendor/bladerunner/bladerunner.php';
}

/**
 * Initialize app
 */
new Municipio\App();
