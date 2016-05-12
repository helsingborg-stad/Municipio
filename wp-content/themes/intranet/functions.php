<?php

define('INTRANET_PATH', get_stylesheet_directory() . '/');

//Include vendor files
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

require_once INTRANET_PATH . 'library/Vendor/Psr4ClassLoader.php';
$loader = new Intranet\Vendor\Psr4ClassLoader();
$loader->addPrefix('Intranet', INTRANET_PATH . 'library');
$loader->addPrefix('Intranet', INTRANET_PATH . 'source/php/');
$loader->register();

new Intranet\App();
