<?php

/**
 * Composer autoloader from municipio
 */

use AcfService\Implementations\NativeAcfService;
use Municipio\HooksRegistrar\HooksRegistrar;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostFactory;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;
use WpService\Implementations\NativeWpService;

if (file_exists(MUNICIPIO_PATH . 'vendor/autoload.php')) {
    require_once MUNICIPIO_PATH . 'vendor/autoload.php';
}

/**
 * Load kirki source
 */
$kirkiFilePaths = [
    rtrim(ABSPATH, '/') . '/../vendor/kirki/kirki.php',
    rtrim(MUNICIPIO_PATH, '/') . '/vendor/kirki/kirki.php'
];

foreach ($kirkiFilePaths as $kirkiFilePath) {
    if (file_exists($kirkiFilePath)) {
        include_once($kirkiFilePath);
        break;
    }
}

/**
 * Public include
 */
require_once MUNICIPIO_PATH . 'library/Public.php';

/**
 * Services.
 */
$wpService  = new NativeWpService();
$acfService = new NativeAcfService();

/**
 * Dependencies.
 */
$schemaDataConfigService = new \Municipio\SchemaData\Config\SchemaDataConfigService($wpService);
$schemaObjectFromPost    = (new SchemaObjectFromPostFactory(
    $schemaDataConfigService,
    $wpService,
    new GetSchemaPropertiesWithParamTypes(),
    new SchemaPropertyValueSanitizer()
))->create();

/**
 * Service helpers.
 */
\Municipio\Helper\AcfService::set($acfService);
\Municipio\Helper\WpService::set($wpService);
\Municipio\Helper\Post::setDependencies(new CreatePostObjectFromWpPost($wpService, $acfService, $schemaObjectFromPost));
\Municipio\SchemaData\Helper\GetSchemaType::setAcfService($acfService);

/**
 * Acf auto import and export
 */
add_action('init', function () use ($wpService) {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('municipio');
    $acfExportManager->setExportFolder(MUNICIPIO_PATH . 'library/AcfFields');
    $autoExportIds = $wpService->applyFilters('Municipio/AcfExportManager/autoExport', array(
        // Blocks
        'block-classic-editpr'                       => 'group_61556c32b3697',
        'block-button'                               => 'group_60acdac5158f2',
        'block-margin'                               => 'group_61bc6134601a0',
        'block-container'                            => 'group_63cfdba21f7fc',
        'options-activate-gutenberg'                 => 'group_60b496c06687c',
        'options-theme-features'                     => 'group_6639e9aa1409f',
        // Terms
        'term-icon-and-colour'                       => 'group_63e6002cc129c',
        // Options
        'options-page-display'                       => 'group_56c33cf1470dc',
        'options-page-navigation'                    => 'group_56d83cff12bb3',
        'options-page-quicklinks-placement'          => 'group_64227d79a7f57',
        'options-page-exclude-from-google-translate' => 'group_646c5d26e3359',
        'options-theme-404'                          => 'group_56d41dbd7e501',
        'options-theme-author'                       => 'group_56caee123c53f',
        'options-theme-color-scheme'                 => 'group_56a0a7dcb5c09',
        'options-theme-custom-css'                   => 'group_57148a9b42fd5',
        'options-theme-custom-js'                    => 'group_573184999aa2c',
        'options-theme-custom-post-type'             => 'group_56b34353ef1eb',
        'options-theme-custom-taxonomy'              => 'group_56c5e23aa271c',
        'options-theme-editor-formats'               => 'group_57ff4f49ac8c1',
        'options-theme-footer-icons'                 => 'group_56af210c945f0',
        'options-theme-footer-layout'                => 'group_56e804931bd1e',
        'options-theme-google-translate'             => 'group_56cc6f8fe86d3',
        'options-theme-header'                       => 'group_56a22a9c78e54',
        'options-theme-header-logotype'              => 'group_56c5d34a261f5',
        'options-theme-header-subtitle'              => 'group_584923bd30bfe',
        'options-theme-header-tagline'               => 'group_56f25b68658cd',
        'options-theme-logotype'                     => 'group_56a0f1f7826dd',
        'options-theme-mobile-navigation'            => 'group_5885cbc79fc34',
        'options-theme-post-timestamps'              => 'group_56cacd2f1873f',
        'options-theme-post-types'                   => 'group_56c6ba934d682',
        'options-theme-primary-navigation'           => 'group_56e935ea546ce',
        'options-theme-scroll-elevator'              => 'group_5825be470579f',
        'options-theme-search'                       => 'group_569fa7edcdd6b',
        'options-theme-search-display'               => 'group_56a72f6430912',
        'options-theme-search-result'                => 'group_586df81d53d0f',
        'options-theme-search-algolia'               => 'group_5a61b852f3f8c',
        'options-theme-share'                        => 'group_56c431971df46',
        'options-theme-sub-navigation'               => 'group_56e941cae1ed2',
        'options-menu-icon'                          => 'group_60c325749aeab',
        'options-menu-style'                         => 'group_61dc486660615',
        'options-menu-floating'                      => 'group_60c86946524a0',
        'options-menu-mega'                          => 'group_6502be085ee3b',
        'options-menu-language'                      => 'group_6141cc9c72cc3',
        'options-api-resources-apis'                 => 'group_653a1673dc501',
        'options-comment-settings'                   => 'group_67173bdc92fde',
        'options-customize-header'                   => 'group_5afa93c0a25e1',
        'options-customize-footer'                   => 'group_5afa94c88e1aa',
        'options-login-logout'                       => 'group_67597150948c7',
        'options-login-redirect'                     => 'group_675aecfbf2f3d',
        'post-icon'                                  => 'group_6784bb5c51d70',
        'resource-fields'                            => 'group_653a509450198',
        'user-author-image'                          => 'group_56c714b46105e',
        'widget-contact'                             => 'group_56c58bade87dc',
        'widget-header-common'                       => 'group_5a65d5e7e913b',
        'widget-header-menu'                         => 'group_5a58ce68e8b61',
        'widget-header-links'                        => 'group_5a6744018083f',
        'theme-version-preview'                      => 'group_5aa14b41551ae',
        'navigation-widget'                          => 'group_5ae64000dd723',
        'widget-media'                               => 'group_5b2b70c0bde2f',
        'media-attachments'                          => 'group_650857c9f2cce',
        'hidden-validation'                          => 'group_654a2a57e6897',
        'user-group-url'                             => 'group_677e6a05e347c',
        'user-group-shortname'                       => 'group_6846e4ecea40b',
        'post-status-conditional'                    => 'group_671241997f07d',
        'common-field-groups'                        => 'group_678e65a73edb3',
        'global-notices'                             => 'group_6798e1aebe3c6',
        'a11y-statement'                             => 'group_6874ffb12b42d',
        'a11y-statement-url'                         => 'group_689c4def19f8e',
    ));

    $acfExportManager->autoExport($autoExportIds);
    $acfExportManager->import();
});

/**
 * Initialize app
 */
if (function_exists('get_field')) {
    global $wpdb;

    new Municipio\App(
        $wpService,
        $acfService,
        new HooksRegistrar(),
        new \Municipio\AcfFieldContentModifiers\Registrar($wpService),
        $schemaDataConfigService,
        $wpdb
    );
} else {
    if (!(defined('WP_CLI') && constant('WP_CLI'))) {
        if (is_admin()) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>To run <strong>Municipio</strong> theme, please install & activate the <a href="http://www.advancedcustomfields.com/pro/">Advanced Custom Fields <strong>PRO</strong></a> plugin.</p></div>'; // phpcs:ignore
            });
        }
        if (!is_admin() && $_SERVER["SCRIPT_FILENAME"] != ABSPATH . "wp-login.php") { // phpcs:ignore
            wp_die('<div class="notice notice-error"><p>To run <strong>Municipio</strong> theme, please install & activate the <a href="http://www.advancedcustomfields.com/pro/">Advanced Custom Fields <strong>PRO</strong></a> plugin.</p>', "ACF Pro Required"); // phpcs:ignore
        }
    }
}
