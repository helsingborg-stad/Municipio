<?php

namespace Municipio\Theme;

use Throwable;

/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue
{
    private const ENQUEUE = [
        'frontend' => [
            ['handle' => 'municipio', 'src' => 'js/municipio.js', 'deps' => ['jquery'], 'in_footer' => true, 'module' => true],
            ['handle' => 'pre-styleguide-js', 'src' => 'js/pre-styleguide.js', 'deps' => [], 'in_footer' => true, 'localize' => [
                'object_name' => 'localizedData',
                'callback' => 'getPreStyleguideLocalization'
            ]],
            ['handle' => 'municipio-js', 'src' => 'js/municipio.js', 'deps' => ['wp-api-request'], 'in_footer' => true, 'module' => true, 'localize' => [
                'object_name' => 'MunicipioLang',
                'callback' => 'getMunicipioTranslations'
            ]],
            ['handle' => 'styleguide-js', 'src' => 'js/styleguide.js', 'deps' => [], 'in_footer' => true],
            ['handle' => 'instantpage-js', 'src' => 'js/instantpage.js', 'deps' => [], 'in_footer' => true],
            ['handle' => 'pdf-js', 'src' => 'js/pdf.js', 'deps' => [], 'in_footer' => true, 'module' => true],
            ['handle' => 'nav-js', 'src' => 'js/nav.js', 'deps' => [], 'in_footer' => true, 'module' => true],
        ],
        'admin' => [
            ['handle' => 'options-reading', 'src' => 'js/options-reading.js', 'deps' => ['jquery'], 'in_footer' => true],
            ['handle' => 'user-group-visibility', 'src' => 'js/user-group-visibility.js', 'deps' => [], 'in_footer' => true],
            ['handle' => 'hidden-post-status-conditional', 'src' => 'js/hidden-post-status-conditional.js', 'deps' => ['acf-input', 'jquery'], 'in_footer' => true],
            ['handle' => 'event-source-progress', 'src' => 'js/event-source-progress.js', 'deps' => [], 'in_footer' => true],
        ],
        'customizer' => [
            ['handle' => 'design-share-js', 'src' => 'js/design-share.js', 'deps' => ['jquery', 'customize-controls'], 'in_footer' => true],
            ['handle' => 'customizer-flexible-header', 'src' => 'js/customizer-flexible-header.js', 'deps' => ['jquery', 'customize-controls'], 'in_footer' => true, 'localize' => [
                'object_name' => 'flexibleHeader',
                'callback' => 'getCustomizerFlexibleHeaderLocalization'
            ]],
            ['handle' => 'customizer-error-handling', 'src' => 'js/customizer-error-handling.js', 'deps' => ['jquery', 'customize-controls'], 'in_footer' => true],
        ],
        'styles' => [
            ['handle' => 'styleguide-css', 'src' => 'css/styleguide.css'],
            ['handle' => 'municipio-css', 'src' => 'css/municipio.css'],
            ['handle' => 'acf-css', 'src' => 'css/acf.css', 'admin' => true],
            ['handle' => 'general-css', 'src' => 'css/general.css', 'admin' => true],
            ['handle' => 'a11y-css', 'src' => 'css/a11y.css', 'admin' => true],
            ['handle' => 'header-flexible', 'src' => 'css/header-flexible.css', 'customizer' => true],
        ]
    ];

    /**
     * Translations and localization callbacks
     */
    private static function getMunicipioTranslations(): array
    {
        return [
            'printbreak' => ['tooltip' => __('Insert Print Page Break tag', 'municipio')],
            'messages'   => [
                'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                'onError'       => __('Something went wrong, please try again later', 'municipio'),
            ]
        ];
    }

    private static function getPreStyleguideLocalization(): array
    {
        return [
            'months' => [
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
            ],
            'days' => [
                ucFirst(__('Su', 'municipio')),
                ucFirst(__('Mo', 'municipio')),
                ucFirst(__('Tu', 'municipio')),
                ucFirst(__('We', 'municipio')),
                ucFirst(__('Th', 'municipio')),
                ucFirst(__('Fr', 'municipio')),
                ucFirst(__('Sa', 'municipio'))
            ]
        ];
    }

    private static function getCustomizerFlexibleHeaderLocalization(): array
    {
        return [
            'hiddenValue' => get_theme_mod('header_sortable_hidden_storage'),
            'lang'        => [
                'alignment' => __('Alignment', 'municipio'),
                'margin'    => __('Margin', 'municipio'),
                'left'      => __('Left', 'municipio'),
                'right'     => __('Right', 'municipio'),
                'both'      => __('Both', 'municipio'),
                'none'      => __('None', 'municipio'),
            ]
        ];
    }

    /**
     * Enqueue constructor.
     */
    public function __construct()
    {
        if (!defined('ASSETS_DIST_PATH')) {
            define('ASSETS_DIST_PATH', '/assets/dist/');
        }
  
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', function () {
            $this->enqueueFromConfig(self::ENQUEUE['frontend'], 'frontend');
            $this->enqueueStyles('frontend');
        }, 5);
        add_action('admin_enqueue_scripts', function () {
            $this->enqueueFromConfig(self::ENQUEUE['admin'], 'admin');
            $this->enqueueStyles('admin');
        }, 999);
        add_action('customize_controls_enqueue_scripts', function () {
            $this->enqueueFromConfig(self::ENQUEUE['customizer'], 'customizer');
            $this->enqueueStyles('customizer');
        }, 999);
        add_action('wp_enqueue_scripts', array($this, 'icons'), 5);
        add_action('admin_enqueue_scripts', array($this, 'icons'), 5);

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

        // Add module type to script tags if module flag is set
        add_filter('script_loader_tag', [$this, 'addModuleTag'], 10, 3);
    }

    /**
     * Adds a module type attribute to script tags.
     */
    public function addModuleTag(string $tag, string $handle, string $src): string
    {
        // Only set type="module" if module flag is set in ENQUEUE
        foreach (self::ENQUEUE as $context => $entries) {
            if (!is_array($entries)) {
                continue;
            }
            foreach ($entries as $entry) {
                if (isset($entry['handle']) && $entry['handle'] === $handle && !empty($entry['module'])) {
                    $tag = str_replace(' src=', ' type="module" src=', $tag);
                    break 2;
                }
            }
        }
        return $tag;
    }

    /**
     * Get cache-busted asset file url.
     */
    private static function getAssetWithCacheBust(string $file): string
    {
        return get_template_directory_uri() . ASSETS_DIST_PATH . \Municipio\Helper\CacheBust::name($file);
    }

    /**
     * Unified enqueue from config
     */
    private function enqueueFromConfig(array $config, string $context): void
    {
        foreach ($config as $entry) {
            $handle = $entry['handle'];
            $src = self::getAssetWithCacheBust($entry['src']);

            if (str_ends_with($entry['src'], '.js')) {
                $deps = $entry['deps'] ?? [];
                $in_footer = $entry['in_footer'] ?? true;
                wp_register_script($handle, $src, $deps, null, $in_footer);
                wp_enqueue_script($handle);

                if (!empty($entry['localize'])) {
                    $objectName = $entry['localize']['object_name'];
                    $callback   = $entry['localize']['callback'];
                    if (method_exists(self::class, $callback)) {
                        $data = forward_static_call([self::class, $callback]);
                        wp_localize_script($handle, $objectName, $data);
                    }
                }
            } elseif (str_ends_with($entry['src'], '.css')) {
                wp_register_style($handle, $src);
                wp_enqueue_style($handle);
            }
        }
    }

    private function enqueueStyles(string $context): void
    {
        foreach (self::ENQUEUE['styles'] as $style) {
            if (!empty($style['admin']) && $context === 'admin') {
                wp_register_style($style['handle'], self::getAssetWithCacheBust($style['src']));
                wp_enqueue_style($style['handle']);
            } elseif (!empty($style['customizer']) && $context === 'customizer') {
                wp_register_style($style['handle'], self::getAssetWithCacheBust($style['src']));
                wp_enqueue_style($style['handle']);
            } elseif (empty($style['admin']) && empty($style['customizer']) && $context === 'frontend') {
                wp_register_style($style['handle'], self::getAssetWithCacheBust($style['src']));
                wp_enqueue_style($style['handle']);
            }
        }
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
