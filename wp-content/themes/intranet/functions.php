<?php

define('INTRANET_PATH', get_stylesheet_directory() . '/');
define('INTRANET_TEMPLATE_PATH', get_stylesheet_directory() . '/templates/');

add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio-intranet', get_stylesheet_directory() . '/languages');
});

//Include vendor files
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

// Acf auto import and export
add_action('after_setup_theme', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('municipio-intranet');
    $acfExportManager->setExportFolder(INTRANET_PATH . 'acf-fields');
    $acfExportManager->autoExport(array(
        'incidents' => 'group_57bade5cb86d5',
        'incidents-module' => 'group_57bb00ff522ad',
        'news-module' => 'group_57469ceda9387',
        'table-of-contents' => 'group_5774dcb335058'
    ));
    $acfExportManager->import();
});

require_once INTRANET_PATH . 'library/Vendor/Psr4ClassLoader.php';
$loader = new Intranet\Vendor\Psr4ClassLoader();
$loader->addPrefix('Intranet', INTRANET_PATH . 'library');
$loader->addPrefix('Intranet', INTRANET_PATH . 'source/php/');
$loader->addPrefix('Intranet', INTRANET_PATH . 'modules');
$loader->register();

new Intranet\App();

require_once INTRANET_PATH . 'library/Public.php';
