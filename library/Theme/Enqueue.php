<?php

namespace Municipio\Theme;

use Municipio\Helper\Enqueue as EnqueueHelper;
use Municipio\Helper\EnqueueTranslation;
use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use WpUtilService\WpUtilService;
/**
 * Class Enqueue
 * @package Municipio\Theme
 */
class Enqueue implements Hookable
{
    /**
     * Enqueue constructor.
     */
    public function __construct(
        private WpService $wpService,
        private WpUtilService $wpUtilService,
        private EnqueueHelper $enqueueHelper
    ) {
        $this->wpUtilService->enqueue(__DIR__);
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', array($this, 'enqueueMaterialSymbols'));
        $this->wpService->addAction('admin_enqueue_scripts', array($this, 'enqueueMaterialSymbols'), 999);
        $this->wpService->addAction('wp_enqueue_scripts', array($this, 'enqueueFrontendScriptsAndStyles'), 5);
        $this->wpService->addAction('admin_enqueue_scripts', array($this, 'enqueueAdminScriptsAndStyles'), 999);
        $this->wpService->addAction('customize_controls_enqueue_scripts', array($this, 'enqueueCustomizerScriptsAndStyles'), 999);
        $this->wpService->addFilter('script_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        $this->wpService->addFilter('style_loader_src', array($this, 'removeScriptVersion'), 15, 1);
        $this->wpService->addFilter('the_generator', array($this, 'removeGeneratorTag'), 9, 2);
        $this->wpService->addAction('wp_print_scripts', array($this, 'moveScriptsToFooter'));
        $this->wpService->addAction('wp_default_scripts', array($this, 'removeJqueryMigrate'));
        $this->wpService->addFilter('gform_init_scripts_footer', array($this, 'forceGravityFormsScriptsNotInFooter'));
    }

    /**
     * Enqueue Material Symbols font CSS
     */
    public function enqueueMaterialSymbols()
    {
        $weight = $this->wpService->getThemeMod('icon_weight') ?: "400";
        $style  = $this->wpService->getThemeMod('icon_style') ?: "rounded";

        $weightTranslationTable = [
            '200' => 'light',
            '400' => 'medium',
            '600' => 'bold',
        ];
        $translatedWeight       = $weightTranslationTable[$weight] ?? 'medium';

        $this->wpUtilService->enqueue()->add(
            "fonts/material/{$translatedWeight}/{$style}.css"
        );
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueueFrontendScriptsAndStyles()
    {
        //Add municipio.js with translations
        $this->wpUtilService->enqueue()->add(
            'js/municipio.js',
            ['jquery', 'wp-api-request']
        )->with()->translation(
            'MunicipioLocale',
            [
                'printbreak' => ['tooltip' => __('Insert Print Page Break tag', 'municipio')],
                'messages'   => [
                    'deleteComment' => __('Are you sure you want to delete the comment?', 'municipio'),
                    'onError'       => __('Something went wrong, please try again later', 'municipio'),
                ]
            ]
        );

        //Add styleguide.js with translations
        $this->wpUtilService->enqueue()->add(
            'js/styleguide.js'
        )->with()->translation('localizedMonths', [
            ucFirst(__('January', 'municipio')),
            ucFirst(__('February', 'municipio')),
            ucFirst(__('March', 'municipio')),
            ucFirst(__('April', 'municipio')),
            ucFirst(__('May', 'municipio')),
            ucFirst(__('June', 'municipio')),
            ucFirst(__('July', 'municipio')),
            ucFirst(__('August', 'municipio')),
            ucFirst(__('September', 'municipio')),
            ucFirst(__('October', 'municipio')),
            ucFirst(__('November', 'municipio')),
            ucFirst(__('December', 'municipio'))
        ])->and()->translation('localizedDays', [
            ucFirst(__('Su', 'municipio')),
            ucFirst(__('Mo', 'municipio')),
            ucFirst(__('Tu', 'municipio')),
            ucFirst(__('We', 'municipio')),
            ucFirst(__('Th', 'municipio')),
            ucFirst(__('Fr', 'municipio')),
            ucFirst(__('Sa', 'municipio'))
        ]);

        //Other scripts
        $this->wpUtilService->enqueue()->add('js/instantpage.js');
        $this->wpUtilService->enqueue()->add('js/pdf.js');
        $this->wpUtilService->enqueue()->add('js/nav.js');

        //Other styles
        $this->wpUtilService->enqueue()->add('css/styleguide.css');
        $this->wpUtilService->enqueue()->add('css/municipio.css');
        $this->wpUtilService->enqueue()->add('css/splide.css');
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueueAdminScriptsAndStyles()
    {
        $this->wpUtilService->enqueue()->add('js/user-group-visibility.js');
        $this->wpUtilService->enqueue()->add('js/hidden-post-status-conditional.js', ['acf-input', 'jquery']);
        $this->wpUtilService->enqueue()->add('js/event-source-progress.js');

        $this->wpUtilService->enqueue()->add('css/acf.css');
        $this->wpUtilService->enqueue()->add('css/general.css');
        $this->wpUtilService->enqueue()->add('css/a11y.css');
        $this->wpUtilService->enqueue()->add('css/trash-page.css');
    }

    /**
     * Enqueue customizer scripts and styles
     */
    public function enqueueCustomizerScriptsAndStyles()
    {
        $this->enqueueHelper->add('design-share-js', 'js/design-share.js', ['jquery', 'customize-controls']);
        $this->enqueueHelper->add('customizer-flexible-header', 'js/customizer-flexible-header.js', ['jquery', 'customize-controls'], new EnqueueTranslation(
            'FlexibleHeaderSettings',
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
    }

    /**
     * Removes querystring from any scripts/styles internally
     *
     * @param string $src The soruce path
     *
     * @return string      The source path without any querystring
     */
    public function removeScriptVersion($src): string
    {
        $siteUrlComponents = parse_url($this->wpService->getSiteUrl());
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
     * Removes generator tag
     */
    public function removeGeneratorTag($a, $b): string
    {
        return '';
    }

    /**
     * Move all scripts to footer, discard settings.
     *
     * @return void
     */
    public function moveScriptsToFooter(): void
    {
        global $wp_scripts;
        $notInFooter           = array_diff($wp_scripts->queue, $wp_scripts->in_footer);
        $wp_scripts->in_footer = array_merge($wp_scripts->in_footer, $notInFooter);
    }

    /**
     * Remove jquery migrate from default scripts
     */
    public function removeJqueryMigrate($scripts): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }
        if (!empty($scripts->registered['jquery'])) {
            $scripts->registered['jquery']->deps = array_diff(
                $scripts->registered['jquery']->deps,
                ['jquery-migrate']
            );
        }
    }

    /**
     * Do not load Gravity Forms scripts in the footer unless you want to work the weekend
     */
    public function forceGravityFormsScriptsNotInFooter(): bool
    {
        return false;
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
                } catch (\Exception $e) {}
            }
        }

        return array_unique($dependencies);
    }
}
