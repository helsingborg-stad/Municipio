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
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if (file_exists(MUNICIPIO_PATH . 'vendor/acf/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    new \Municipio\Acf();

    if (!class_exists('acf')) {
        require_once MUNICIPIO_PATH . 'vendor/acf/acf.php';
    }
}

/**
 * ACF Addon
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if (file_exists(MUNICIPIO_PATH . 'plugins/advanced-custom-fields-font-awesome/acf-font-awesome.php')) {
    require_once MUNICIPIO_PATH . 'plugins/advanced-custom-fields-font-awesome/acf-font-awesome.php';
}

/**
 * Initialize app
 */
if (function_exists('get_field')) {
    new Municipio\App();
} else {

    //Be shure to enable ACF
    if (!is_admin()) {
        wp_redirect(admin_url('plugins.php'));
        exit;
    } else {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Please active ACF (PRO) to proceed.</p></div>';
        });
    }

}
