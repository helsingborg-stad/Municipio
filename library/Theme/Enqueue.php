<?php

namespace Municipio\Theme;

use Throwable;

/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue
{
    private $scriptsExcludedFromDefer = ['wp-i18n'];

    public function __construct()
    {
        if (!defined('ASSETS_DIST_PATH')) {
            define('ASSETS_DIST_PATH', '/assets/dist/');
        }
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'), 5);
        add_action('wp_enqueue_scripts', array($this, 'script'), 5);

        // Enqueue customizer scripts and styles
        add_action('customize_controls_enqueue_scripts', array($this, 'customizeScript'));

        // Admin style
        add_action('admin_enqueue_scripts', array($this, 'adminStyle'), 999);

        add_action( 'admin_enqueue_scripts', array($this, 'adminScript'), 10);

        // Removes version querystring from scripts and styles
        add_filter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        add_filter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);

        // Removes generator tag
        add_filter('the_generator', function ($a, $b) {
            return '';
        }, 9, 2);

        //Move scripts to footer
        add_action('wp_print_scripts', array($this, 'moveScriptsToFooter'));

        //Enable defered loading
        add_filter('script_loader_tag', array($this, 'deferedLoadingJavascript'), 10, 2);

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
            'customizer-error-handling',
            self::getAssetWithCacheBust('js/customizer-error-handling.js'),
            array('jquery', 'customize-controls'),
            false,
            true
        );
    }

    public function adminScript() {
        
    }

    /**
     * Enqueue admin style
     * @return void
     */
    public function adminStyle()
    {
        //Download and use material icons
        $this->getMaterialIcons(null); //Create self handle
    }

    public function gutenbergStyle()
    {
        // Load styleguide css
        wp_register_style('styleguide-css', self::getAssetWithCacheBust('css/styleguide.css'));
        wp_enqueue_style('styleguide-css');

        // Load local municipio css
        wp_register_style('municipio-css', self::getAssetWithCacheBust('css/municipio.css'));
        wp_enqueue_style('municipio-css');

        //Download and use material icons
        $this->getMaterialIcons('municipio-css');
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        // Load styleguide css
        wp_register_style('styleguide-css', self::getAssetWithCacheBust('css/styleguide.css'));
        wp_enqueue_style('styleguide-css');

        // Load local municipio css
        wp_register_style('municipio-css', self::getAssetWithCacheBust('css/municipio.css'));
        wp_enqueue_style('municipio-css');

        //Download and use material icons
        $this->getMaterialIcons('municipio-css');
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
            'messages' => array(
                'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                'onError' => __('Something went wrong, please try again later', 'municipio'),
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
        wp_register_script('instantpage-js', self::getAssetWithCacheBust('js/instantpage.js'));
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
        $notInFooter = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
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
        $urlComponents = parse_url($src);

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
     * Making deffered loading of scripts a posibillity (removes unwanted renderblocking js)
     *
     * @param string $tag    HTML Script tag
     * @param string $handle Script handle
     *
     * @return string         The script tag
     */
    public function deferedLoadingJavascript($tag, $handle)
    {
        if (in_array($handle, $this->getAllScriptsToBeExcludedFromDefer())) {
            return $tag;
        }

        if (is_admin()) {
            return $tag;
        }

        if (isset($_GET['preview']) && $_GET['preview'] == 'true') {
            return $tag;
        }

        global $wp_customize;
        if (isset($wp_customize)) {
            add_filter('Municipio/Theme/Enqueue/deferedLoadingJavascript/handlesToIgnore', function ($handles) {
                $handles[] = 'webfont-loader';
                return $handles;
            }, 10, 3);
        }

        $scriptsHandlesToIgnore = apply_filters('Municipio/Theme/Enqueue/deferedLoadingJavascript/handlesToIgnore', ['readspeaker', 'jquery-core', 'jquery-migrate'], $handle);
        $disableDeferedLoading = apply_filters('Municipio/Theme/Enqueue/deferedLoadingJavascript/disableDeferedLoading', false);

        if (in_array($handle, $scriptsHandlesToIgnore) || $disableDeferedLoading) {
            return $tag;
        }

        return str_replace(' src', ' defer="defer" src', $tag);
    }

    private function getAllScriptsToBeExcludedFromDefer(): array
    {
        $excluded = $this->scriptsExcludedFromDefer;
        foreach ($excluded as $script) {
            try {
                $excluded = array_merge($excluded, $this->getScriptDependencies($script));
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        return $excluded;
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

    /**
     * Download a local copy of material icons and enqueue it inline
     *
     * @param string $enqueueAfter  A exising css handle to enqueue the font to
     * @return void
     */
    private function getMaterialIcons($enqueueAfter = 'municipio-css')
    {
        //Create fake handle if not set
        if (is_null($enqueueAfter)) {
            $enqueueAfter = 'material-icons-handle';
            wp_register_style($enqueueAfter, false);
            wp_enqueue_style($enqueueAfter);
        }

        //Download and use material icons
        $webFontDownloader = new \Kirki\Module\Webfonts\Downloader();
        wp_add_inline_style(
            $enqueueAfter,
            $webFontDownloader->get_styles(
                'https://fonts.googleapis.com/icon?family=Material+Icons'
            )
        );
    }
}
