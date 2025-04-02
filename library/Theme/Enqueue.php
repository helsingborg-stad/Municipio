<?php

namespace Municipio\Theme;

use Throwable;

/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue
{
    /**
     * Enqueue constructor.
     */
    public function __construct()
    {
        if (!defined('ASSETS_DIST_PATH')) {
            define('ASSETS_DIST_PATH', '/assets/dist/');
        }

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'), 5);
        add_action('wp_enqueue_scripts', array($this, 'icons'), 5);
        add_action('wp_enqueue_scripts', array($this, 'script'), 5);

        // Enqueue customizer scripts and styles
        add_action('customize_controls_enqueue_scripts', array($this, 'customizeScript'));

        // Admin style
        add_action('admin_enqueue_scripts', array($this, 'adminStyle'), 999);
        add_action('admin_enqueue_scripts', array($this, 'icons'), 5);

        // Admin scripts
        add_action('admin_enqueue_scripts', array($this, 'adminScripts'), 999);
        // Customizer specific
        add_action('customize_controls_enqueue_scripts', array($this, 'customizerScripts'), 999);

        // Removes version querystring from scripts and styles
        add_filter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        add_filter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);

        // Removes generator tag
        add_filter('the_generator', function ($a, $b) {
            return '';
        }, 9, 2);

        //Move scripts to footer
        add_action('wp_print_scripts', array($this, 'moveScriptsToFooter'));

        //Remove jqmigrate (creates console log error)
        add_action('wp_default_scripts', function ($scripts) {
            if (is_admin()) {
                return;
            }
            if (!empty($scripts->registered['jquery'])) {
                $scripts->registered['jquery']->deps = array_diff(
                    $scripts->registered['jquery']->deps,
                    ['jquery-migrate']
                );
            }
        });

        // Do not load Gravity Forms scripts in the footer unless you want to work the weekend
        add_filter('gform_init_scripts_footer', '__return_false');
    }

    /**
     * Get cache-busted asset file url.
     */
    private static function getAssetWithCacheBust(string $file): string
    {
        return get_template_directory_uri() . ASSETS_DIST_PATH . \Municipio\Helper\CacheBust::name($file);
    }

    /**
     * Enqueue customizer scripts
     * @return void
     */
    public function customizeScript()
    {
        wp_enqueue_script(
            'design-share-js',
            self::getAssetWithCacheBust('js/design-share.js'),
            array('jquery', 'customize-controls'),
            false,
            true
        );

        wp_enqueue_script(
            'customizer-flexible-header',
            self::getAssetWithCacheBust('js/customizer-flexible-header.js'),
            array('jquery', 'customize-controls'),
            false,
            true
        );

        wp_localize_script(
            'customizer-flexible-header',
            'flexibleHeader',
            [
                'hiddenValue' => get_theme_mod('header_sortable_hidden_storage'),
                'lang'        => [
                    'alignment' => __('Alignment', 'municipio'),
                    'margin'    => __('Margin', 'municipio'),
                    'left'      => __('Left', 'municipio'),
                    'right'     => __('Right', 'municipio'),
                    'both'      => __('Both', 'municipio'),
                    'none'      => __('None', 'municipio'),
                ]
            ]
        );

        wp_enqueue_script(
            'customizer-error-handling',
            self::getAssetWithCacheBust('js/customizer-error-handling.js'),
            array('jquery', 'customize-controls'),
            false,
            true
        );
    }

    /**
     * Enqueue admin style
     * @return void
     */
    public function adminStyle()
    {
        wp_register_style('acf-css', self::getAssetWithCacheBust('css/acf.css'));
        wp_enqueue_style('acf-css');

        wp_register_style('general-css', self::getAssetWithCacheBust('css/general.css'));
        wp_enqueue_style('general-css');
    }

    /**
     * Enqueue admin style
     * @return void
     */
    public function customizerScripts()
    {
        wp_register_style('header-flexible', self::getAssetWithCacheBust('css/header-flexible.css'));
        wp_enqueue_style('header-flexible');
    }

     /**
     * Enqueue admin script
     * @return void
     */
    public function adminScripts(): void
    {
        global $pagenow;
        if ($pagenow == 'options-reading.php') {
            wp_enqueue_script(
                'options-reading',
                self::getAssetWithCacheBust('js/options-reading.js'),
                array('jquery'),
                null,
                true
            );
        }

        wp_enqueue_script(
            'user-group-visibility',
            self::getAssetWithCacheBust('js/user-group-visibility.js'),
            array(),
            false,
            true
        );

        wp_enqueue_script(
            'hidden-post-status-conditional',
            self::getAssetWithCacheBust('js/hidden-post-status-conditional.js'),
            array('acf-input', 'jquery'),
            false,
            true
        );

        wp_enqueue_script(
            'event-source-progress',
            self::getAssetWithCacheBust('js/event-source-progress.js'),
            array(),
            false,
            true
        );
    }

     /**
     * Enqueue gutenberg style
     * @return void
     */
    public function gutenbergStyle()
    {
        // Load styleguide css
        wp_register_style('styleguide-css', self::getAssetWithCacheBust('css/styleguide.css'));
        wp_enqueue_style('styleguide-css');

        // Load local municipio css
        wp_register_style('municipio-css', self::getAssetWithCacheBust('css/municipio.css'));
        wp_enqueue_style('municipio-css');
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        // Remove default block styles
        wp_deregister_style('wp-block-library');
        wp_dequeue_style('wp-block-library');

        // Load styleguide css
        wp_register_style('styleguide-css', self::getAssetWithCacheBust('css/styleguide.css'));
        wp_enqueue_style('styleguide-css');

        // Load local municipio css
        wp_register_style('municipio-css', self::getAssetWithCacheBust('css/municipio.css'));
        wp_enqueue_style('municipio-css');
    }

    /**
     * Enqueue icons
     * Enqueues the selected icon font style.
     *
     * @return void
     */
    public function icons()
    {
        $weight = get_theme_mod('icon_weight') ?: "400";
        $style  = get_theme_mod('icon_style') ?: "rounded";

        $weightTranslationTable = [
            '200' => 'light',
            '400' => 'medium',
            '600' => 'bold',
        ];

        wp_register_style('material-symbols', self::getAssetWithCacheBust(
            sprintf(
                'fonts/material/%s/%s.css',
                $weightTranslationTable[$weight] ?? 'medium',
                $style
            )
        ));
        wp_enqueue_style('material-symbols');
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script()
    {
        //Language & parameters
        wp_localize_script('municipio', 'MunicipioLang', array(
            'printbreak' => array(
                'tooltip' => __('Insert Print Page Break tag', 'municipio')
            ),
            'messages'   => array(
                'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                'onError'       => __('Something went wrong, please try again later', 'municipio'),
            )
        ));

        //Comment reply
        if (is_singular() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        wp_register_script('pre-styleguide-js', false, false, false, true);

        wp_localize_script('pre-styleguide-js', 'localizedMonths', array(
            ucFirst(__('January')),
            ucFirst(__('February')),
            ucFirst(__('March')),
            ucFirst(__('April')),
            ucFirst(__('May')),
            ucFirst(__('June')),
            ucFirst(__('July')),
            ucFirst(__('August')),
            ucFirst(__('September')),
            ucFirst(__('October')),
            ucFirst(__('November')),
            ucFirst(__('December'))
        ));

        wp_localize_script('pre-styleguide-js', 'localizedDays', array(
            ucFirst(__('Su', 'municipio')),
            ucFirst(__('Mo', 'municipio')),
            ucFirst(__('Tu', 'municipio')),
            ucFirst(__('We', 'municipio')),
            ucFirst(__('Th', 'municipio')),
            ucFirst(__('Fr', 'municipio')),
            ucFirst(__('Sa', 'municipio'))
        ));

        wp_enqueue_script('pre-styleguide-js');

        //Load local styleguide js
        wp_register_script('styleguide-js', self::getAssetWithCacheBust('js/styleguide.js'));
        wp_enqueue_script('styleguide-js');

        //Load local municipio js
        wp_register_script('municipio-js', self::getAssetWithCacheBust('js/municipio.js'), array('wp-api-request'));
        wp_enqueue_script('municipio-js');

        //Load instant page
        wp_register_script(
            'instantpage-js',
            self::getAssetWithCacheBust('js/instantpage.js'),
            [],
            null,
            [
                'strategy'  => 'defer',
                'in_footer' => true
            ]
        );
        wp_enqueue_script('instantpage-js');

        //Load pdf generator
        wp_register_script('pdf-js', self::getAssetWithCacheBust('js/pdf.js'));
        wp_enqueue_script('pdf-js');
    }

    /**
     * Move all scripts to footer, discard settings.
     *
     * @return void
     */
    public function moveScriptsToFooter()
    {
        global $wp_scripts;
        $notInFooter           = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
        $wp_scripts->in_footer = array_merge($wp_scripts->in_footer, $notInFooter);
    }

    /**
     * Removes querystring from any scripts/styles internally
     *
     * @param string $src The soruce path
     *
     * @return string      The source path without any querystring
     */
    public function removeScriptVersion($src)
    {
        $siteUrlComponents = parse_url(get_site_url());
        $urlComponents     = parse_url($src);

        // Check if the URL is internal or external
        if (
            !empty($siteUrlComponents['host'])
            && !empty($urlComponents['host'])
            && strcasecmp($urlComponents['host'], $siteUrlComponents['host']) === 0
            && !is_admin_bar_showing()
        ) {
            $src = !empty($urlComponents['query']) ? str_replace('?' . $urlComponents['query'], '', $src) : $src;
            return $src;
        } else {
            return $src;
        }
    }

    /**
     * Get script all dependencies recusively.
     *
     * @param string $script The script handle
     * @return array         The script dependencies
     */
    public function getScriptDependencies($script): array
    {
        global $wp_scripts;

        if (!isset($wp_scripts->registered[$script])) {
            trigger_error("Script \"$script\" is not registered.", E_USER_WARNING);
        }

        $dependencies = $wp_scripts->registered[$script]->deps;

        foreach ($dependencies as $dependency) {
            if (!empty($wp_scripts->registered[$dependency]->deps)) {
                try {
                    $dependencies = array_merge($dependencies, $this->getScriptDependencies($dependency));
                } catch (\Exception $e) {
                    // Do nothing
                }
            }
        }

        return array_unique($dependencies);
    }
}
