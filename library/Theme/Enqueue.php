<?php

namespace Municipio\Theme;

use Municipio\Helper\Enqueue as EnqueueHelper;
use Municipio\Helper\EnqueueTranslation;

/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue
{
    /**
     * Enqueue constructor.
     */
    public function __construct(private EnqueueHelper $enqueueHelper)
    {
        add_action('wp_enqueue_scripts', function () {
            $weight = get_theme_mod('icon_weight') ?: "400";
            $style  = get_theme_mod('icon_style') ?: "rounded";

            $weightTranslationTable = [
                '200' => 'light',
                '400' => 'medium',
                '600' => 'bold',
            ];

            $this->enqueueHelper->add(
                'material-symbols',
                "fonts/material/{($weightTranslationTable[$weight] ?? 'medium')}/{$style}.css"
            );
        });

        // Enqueue frontend scripts and styles
        add_action('wp_enqueue_scripts', function () {
            $this->enqueueHelper->add('municipio', 'js/municipio.js', ['jquery']);
            $this->enqueueHelper->add('municipio-js', 'js/municipio.js', ['wp-api-request'], new EnqueueTranslation(
                'MunicipioLocale',
                [
                    'printbreak' => ['tooltip' => __('Insert Print Page Break tag', 'municipio')],
                    'messages'   => [
                        'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                        'onError'       => __('Something went wrong, please try again later', 'municipio'),
                    ]
                ]
            ));
            $this->enqueueHelper->add('styleguide-js', 'js/styleguide.js', [], new EnqueueTranslation(
                'StyleguideLocale',
                [
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
                ]
            ));
            $this->enqueueHelper->add('instantpage-js', 'js/instantpage.js');
            $this->enqueueHelper->add('pdf-js', 'js/pdf.js');
            $this->enqueueHelper->add('nav-js', 'js/nav.js');

            $this->enqueueHelper->add('styleguide-css', 'css/styleguide.css');
            $this->enqueueHelper->add('municipio-css', 'css/municipio.css');
        }, 5);

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', function () {
            $this->enqueueHelper->add('options-reading', 'js/options-reading.js', ['jquery']);
            $this->enqueueHelper->add('user-group-visibility', 'js/user-group-visibility.js');
            $this->enqueueHelper->add('hidden-post-status-conditional', 'js/hidden-post-status-conditional.js', ['acf-input', 'jquery']);
            $this->enqueueHelper->add('event-source-progress', 'js/event-source-progress.js');

            $this->enqueueHelper->add('acf-css', 'css/acf.css');
            $this->enqueueHelper->add('general-css', 'css/general.css');
            $this->enqueueHelper->add('a11y-css', 'css/a11y.css');
        }, 999);

        // Enqueue customizer scripts and styles
        add_action('customize_controls_enqueue_scripts', function () {
            $this->enqueueHelper->add('design-share-js', 'js/design-share.js', ['jquery', 'customize-controls']);
            $this->enqueueHelper->add('customizer-flexible-header', 'js/customizer-flexible-header.js', ['jquery', 'customize-controls'], new EnqueueTranslation(
                'HeaderLocale',
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
            ));
            $this->enqueueHelper->add('customizer-error-handling', 'js/customizer-error-handling.js', ['jquery', 'customize-controls']);

            $this->enqueueHelper->add('header-flexible', 'css/header-flexible.css');
        }, 999);

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
