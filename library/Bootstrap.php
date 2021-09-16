<?php

/**
 * Composer autoloader from municipio
 */
if (file_exists(MUNICIPIO_PATH . 'vendor/autoload.php')) {
    require_once MUNICIPIO_PATH . 'vendor/autoload.php';
}

/**
 * Composer autoloader from abspath
 */
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

/**
 * Psr4ClassLoader
 */
require_once MUNICIPIO_PATH . 'library/Vendor/Psr4ClassLoader.php';
require_once MUNICIPIO_PATH . 'library/Public.php';

/**
 * Initialize autoloader (psr4)
 */
$loader = new Municipio\Vendor\Psr4ClassLoader();
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'library');
$loader->addPrefix('Municipio', MUNICIPIO_PATH . 'source/php/');
$loader->register();

/**
 * Acf auto import and export
 */
add_action('init', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('municipio');
    $acfExportManager->setExportFolder(MUNICIPIO_PATH . 'library/AcfFields');
    $acfExportManager->autoExport(array(
        'options-page-display'                      => 'group_56c33cf1470dc',
        'options-page-navigation'                   => 'group_56d83cff12bb3',
        'options-theme-404'                         => 'group_56d41dbd7e501',
        'options-theme-author'                      => 'group_56caee123c53f',
        'options-theme-color-scheme'                => 'group_56a0a7dcb5c09',
        'options-theme-custom-css'                  => 'group_57148a9b42fd5',
        'options-theme-custom-js'                   => 'group_573184999aa2c',
        'options-theme-custom-post-type'            => 'group_56b34353ef1eb',
        'options-theme-custom-taxonomy'             => 'group_56c5e23aa271c',
        'options-theme-editor-formats'              => 'group_57ff4f49ac8c1',
        'options-theme-favicon'                     => 'group_56cc39aba8782',
        'options-theme-footer-icons'                => 'group_56af210c945f0',
        'options-theme-footer-layout'               => 'group_56e804931bd1e',
        'options-theme-footer-logotype'             => 'group_56c5d41852a31',
        'options-theme-google-translate'            => 'group_56cc6f8fe86d3',
        'options-theme-header'                      => 'group_56a22a9c78e54',
        'options-theme-header-logotype'             => 'group_56c5d34a261f5',
        'options-theme-header-subtitle'             => 'group_584923bd30bfe',
        'options-theme-header-tagline'              => 'group_56f25b68658cd',
        'options-theme-logotype'                    => 'group_56a0f1f7826dd',
        'options-theme-mobile-navigation'           => 'group_5885cbc79fc34',
        'options-theme-post-timestamps'             => 'group_56cacd2f1873f',
        'options-theme-post-types'                  => 'group_56c6ba934d682',
        'options-theme-primary-navigation'          => 'group_56e935ea546ce',
        'options-theme-fixed-action-bar'            => 'group_5a2957d095e95',
        'options-theme-scroll-elevator'             => 'group_5825be470579f',
        'options-theme-search'                      => 'group_569fa7edcdd6b',
        'options-theme-search-display'              => 'group_56a72f6430912',
        'options-theme-search-result'               => 'group_586df81d53d0f',
        'options-theme-search-algolia'              => 'group_5a61b852f3f8c',
        'options-theme-share'                       => 'group_56c431971df46',
        'options-theme-sub-navigation'              => 'group_56e941cae1ed2',
        'options-menu-icon'                         => 'group_60c325749aeab',
        'options-menu-floating'                     => 'group_60c86946524a0',
        'options-menu-language'                     => 'group_6141cc9c72cc3',
        'user-author-image'                         => 'group_56c714b46105e',
        'widget-contact'                            => 'group_56c58bade87dc',
        'widget-header-common'                      => 'group_5a65d5e7e913b',
        'widget-header-menu'                        => 'group_5a58ce68e8b61',
        'widget-header-links'                       => 'group_5a6744018083f',
        'theme-version-preview'                     => 'group_5aa14b41551ae',
        'navigation-widget'                         => 'group_5ae64000dd723',
        'options-customize-header'                  => 'group_5afa93c0a25e1',
        'options-customize-footer'                  => 'group_5afa94c88e1aa',
        'widget-media'                              => 'group_5b2b70c0bde2f',

        //Customizer
        'customizer-menu-position'                  => 'group_60cb4dd20e9c3',

        'customizer-design-share-load'              => 'group_604a117fe6872', //Translate

        //Design
        'customizer-color'                          => 'group_60361b6d86d9d', //Translate
        'customizer-radius'                         => 'group_603662f315acc',
        'customizer-width'                          => 'group_60928d240f1bf',
        'customizer-padding'                        => 'group_614331a86b081',
        'customizer-header'                         => 'group_6143452439e4a',
        'customizer-mobilemenu'                     => 'group_6143458359420',

        //Modifiers
        'customizer-contacts'                       => 'group_614336d78b898',
        'customizer-event'                          => 'group_6143374aa9575',
        'customizer-form'                           => 'group_6143379386443',
        'customizer-index'                          => 'group_614337a28e50a',
        'customizer-inlay'                          => 'group_614336f51a8f2',
        'customizer-jsonrender'                     => 'group_61433782358ee',
        'customizer-localevent'                     => 'group_614337b46f273',
        'customizer-map'                            => 'group_6143370287209',
        'customizer-posts'                          => 'group_614336c3d5885',
        'customizer-sectionssplit'                  => 'group_614337ce54b5f',
        'customizer-text'                           => 'group_614337104a06d',
        'customizer-video'                          => 'group_6143373e63d5d',
        'customizer-script'                         => 'group_6143373e63d5d'
    ));
    
    $acfExportManager->import();
});

/**
 * Initialize app
 */
if (function_exists('get_field')) {
    new Municipio\App();
} else {
    if (!(defined('WP_CLI') && WP_CLI)) {
        if (is_admin()) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>To run <strong>Municpio</strong> theme, please install & activate the <a href="http://www.advancedcustomfields.com/pro/">Advanced Custom Fields <strong>PRO</strong></a> plugin.</p></div>';
            });
        }
        if (!is_admin() && $_SERVER["SCRIPT_FILENAME"] != ABSPATH . "wp-login.php") {
            wp_die('<div class="notice notice-error"><p>To run <strong>Municpio</strong> theme, please install & activate the <a href="http://www.advancedcustomfields.com/pro/">Advanced Custom Fields <strong>PRO</strong></a> plugin.</p>', "ACF Pro Required");
        }
    }
}
