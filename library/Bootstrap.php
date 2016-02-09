<?php

define('MUNICIPIO_PATH', get_template_directory() . '/');

/**
 * Composer autoloader
 */
if (file_exists(MUNICIPIO_PATH . 'vendor/autoload.php')) {
    require_once MUNICIPIO_PATH . 'vendor/autoload.php';
}

//Include vendor files
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

/**
 * Psr4ClassLoader
 */
require_once MUNICIPIO_PATH . 'library/Vendor/Psr4ClassLoader.php';

require_once MUNICIPIO_PATH . 'library/Public.php';

$loader = new Municipio\Vendor\Psr4ClassLoader();
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'library');
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'source/php/');
$loader->register();

/**
 * ACF
 */
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if (file_exists(MUNICIPIO_PATH . 'vendor/acf/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    new \Municipio\Acf();

    if (!class_exists('acf')) {
        require_once MUNICIPIO_PATH . 'vendor/acf/acf.php';
    }
}

/**
 * ACF Addon
 */
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if (file_exists(MUNICIPIO_PATH . 'vendor/advanced-custom-fields-font-awesome/acf-font-awesome.php')) {
    require_once MUNICIPIO_PATH . 'vendor/advanced-custom-fields-font-awesome/acf-font-awesome.php';
}


/**
 * Initialize app
 */
new Municipio\App();
