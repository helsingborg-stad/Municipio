<?php

define('INTRANET_PATH', get_stylesheet_directory() . '/');

add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio-intranet', get_stylesheet_directory() . '/languages');
});

//Include vendor files
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

require_once INTRANET_PATH . 'library/Vendor/Psr4ClassLoader.php';
$loader = new Intranet\Vendor\Psr4ClassLoader();
$loader->addPrefix('Intranet', INTRANET_PATH . 'library');
$loader->addPrefix('Intranet', INTRANET_PATH . 'source/php/');
$loader->addPrefix('Intranet', INTRANET_PATH . 'modules');
$loader->register();

new Intranet\App();

require_once INTRANET_PATH . 'library/Public.php';
