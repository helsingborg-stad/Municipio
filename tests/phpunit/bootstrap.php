<?php

use tad\FunctionMocker\FunctionMocker;

require_once dirname(__DIR__) . '/../vendor/autoload.php';

// Bootstrap Patchwork
WP_Mock::setUsePatchwork(true);

// Bootstrap WP_Mock to initialize built-in features
WP_Mock::bootstrap();

WP_Mock::userFunction('plugin_dir_path')->andReturn('./');
WP_Mock::userFunction('plugins_url')->andReturn('foo');
WP_Mock::userFunction('load_plugin_textdomain')->andReturn('foo');
WP_Mock::userFunction('get_template_directory')->andReturn('/');
WP_Mock::userFunction('plugin_basename')->andReturn('foo');
WP_Mock::userFunction('is_wp_error', [
    'return' => function ($object) {
        return $object instanceof WP_Error;
    }
]);

FunctionMocker::init([
    'include' => [dirname(__DIR__)]
]);
