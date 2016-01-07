<?php

define('MUNICIPIO_PATH', dirname(__FILE__) . '/');

require_once MUNICIPIO_PATH . 'Vendor/Psr4ClassLoader.php';

$loader = new Municipio\Vendor\Psr4ClassLoader();
$loader->addPrefix('Municipio', MUNICIPIO_PATH);
$loader->addPrefix('Municipio', MUNICIPIO_PATH.'source/php/');
$loader->register();

new Municipio\App();
