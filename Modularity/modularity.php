<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

use AcfService\Implementations\NativeAcfService;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use WpService\Implementations\NativeWpService;
use WpUtilService\WpUtilService;

define('MODULARITY_PATH', plugin_dir_path(__FILE__));
define('MODULARITY_URL', get_template_directory_uri() . '/Modularity/');

define('MODULARITY_TEMPLATE_PATH', MODULARITY_PATH . 'templates/');
define('MODULARITY_MODULE_PATH', MODULARITY_PATH . 'source/php/Module/');
define('MODULARITY_MODULE_URL', MODULARITY_URL . '/source/php/Module/');

require_once MODULARITY_PATH . 'Public.php';

// Set services
WpService::set(new NativeWpService());
AcfService::set(new NativeAcfService());

// Acf auto import and export
add_action('after_setup_theme', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('municipio');
    $acfExportManager->setExportFolder(MODULARITY_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'mod-booking' => 'group_56a89f42b432b',
        'mod-contact-info' => 'group_56a0a3928c017',
        'mod-contact-contacts' => 'group_5757b93da8d5c',
        'mod-contacts' => 'group_5805e5dc0a3be',
        'mod-files' => 'group_5756ce3e48782',
        'mod-fileslist' => 'group_5756ce3e48783',
        'mod-gallery' => 'group_5666af6d26b7c',
        'mod-iframe' => 'group_56c47016ea9d5',
        'mod-image' => 'group_570770ab8f064',
        'mod-inheritpost' => 'group_56a8b4fd3567b',
        'mod-inlaylist' => 'group_569e054a7f9c2',
        'mod-latest' => 'group_56a8c4581d906',
        'mod-mainnews' => 'group_569e401dd4422',
        'mod-notice' => 'group_575a842dd1283',
        'mod-posts-displau' => 'group_571dfd3c07a77',
        'mod-posts-filtering' => 'group_571e045dd555d',
        'mod-posts-sorting' => 'group_571dffc63090c',
        'mod-posts-source' => 'group_571dfaabc3fc5',
        'mod-posts-taxonomydisplay' => 'group_630645d822841',
        'mod-rss' => 'group_59535d940706c',
        'mod-script' => 'group_56a8b9eddfced',
        'mod-slider' => 'group_56a5e99108991',
        'mod-table' => 'group_5666a2a71d806',
        'mod-text' => 'group_5891b49127038',
        'mod-video' => 'group_57454ae7b0e9a',
        'mod-map' => 'group_602400d904b59',
        'mod-curator' => 'group_609b788ad04bb',
        'mod-spacer' => 'group_611cffa40276a',
        'mod-table-block' => 'group_60b8bf5bbc4d7',
        'mod-text-block' => 'group_60ab6d6ba3621',
        'mod-hero' => 'group_614b3f1a751bf',
        'mod-hero-display-as' => 'group_63ca5ed0cb7f4',
        'mod-logogrid' => 'group_61bc951d73494',
        'mod-divider' => 'group_62816d604ae46',
        'mod-all' => 'group_636e424039120',
        'mod-subscribe' => 'group_641c51b765f4b',
        'mod-modal' => 'group_64a29154aa972',
        'mod-manual-input' => 'group_64ff22b117e2c',
        'mod-menu' => 'group_66c34c64b8d10',
        'mod-audio' => 'group_66d0837591221',
        'mod-search' => 'group_66dffe0be28c1',
        'mod-markdown' => 'group_67506ac21d132',
        'mod-interactive-map' => 'group_67a6218f4b8a6',
        // Deactivated
        'mod-social' => 'group_56dedc26e5327',
        'mod-wpwidget' => 'group_5729f4d3e7c7a',
        'mod-sites' => 'group_58ecb6b6330f4',
        'mod-index' => 'group_569ceab2c16ee',
    ));
    $acfExportManager->import();
});

// Start application
add_action(
    'after_setup_theme',
    function () {
        new Modularity\App(new WpUtilService(WpService::get()));
    },
    20,
);
