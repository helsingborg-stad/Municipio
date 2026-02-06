<?php

namespace Municipio\Admin\UI;

class BackEnd
{
    public function __construct()
    {
        add_action('admin_footer', [$this, 'hostingEnviroment']);
        add_action('admin_title', [$this, 'prefixTitle']);
        add_action('wp_title', [$this, 'prefixTitle']);

        // Adds inline styles (css variables) to backend and block editor.
        add_filter('kirki_inline_styles', [$this, 'addKirkiStylesToOption'], 99, 1);
        add_action('customize_save_after', [$this, 'customizeSaveAfter']);
        add_action('admin_head', [$this, 'addCssVarsToBackend']);
        add_action('enqueue_block_assets', [$this, 'addCssVarsToBlockEditor']);
    }

    /**
     * If the option contains data we add it to the DOM.
     */
    public function addCssVarsToBackend()
    {
        $styles = get_option('kirki_inline_styles');
        if (!empty($styles)) {
            echo '<style type="text/css" id="kirki_inline_styles">' . $styles . '</style>';
        }
    }

    /**
     * Add css variables to block editor
     */
    public function addCssVarsToBlockEditor(): void
    {
        $styles = get_option('kirki_inline_styles');
        if (!empty($styles)) {
            wp_add_inline_style('wp-block-library', $styles);
        }
    }

    /**
     * Used to save the inline styles when the option is empty.
     */
    public function addKirkiStylesToOption($styles)
    {
        $customizerInlineStyles = get_option('kirki_inline_styles');
        if (empty($customizerInlineStyles) && !empty($styles)) {
            update_option('kirki_inline_styles', $styles);
        }

        return $styles;
    }

    /**
     * When the customizer is saved we remove data from the option
     */
    public function customizeSaveAfter($el)
    {
        update_option('kirki_inline_styles', null);
    }

    public function prefixTitle($title)
    {
        if (!$this->isLocal() && !$this->isTest() && !$this->isBeta()) {
            return $title;
        }

        $prefix = null;

        if ($this->isLocal()) {
            $prefix = __('Local', 'municipio');
        }

        if ($this->isTest()) {
            $prefix = __('Test', 'municipio');
        }

        if ($this->isBeta()) {
            $prefix = __('Beta', 'municipio');
        }

        return '(' . $prefix . ') ' . $title;
    }

    public function hostingEnviroment()
    {
        // Editor testing zone
        if ($this->isLocal()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __('Notice', 'municipio') . ': </strong>' . __('You\'re on a local server.', 'municipio') . '</div>';
        }

        // Editor testing zone
        if ($this->isTest()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __('Notice', 'municipio') . ': </strong>' . __('This it the test-environment. Your content will not be published.', 'municipio') . '</div>';
        }

        // Developer
        if ($this->isBeta()) {
            echo
                '<div class="hosting-enviroment hosting-red"><strong>'
                    . __('Notice', 'municipio')
                    . ': </strong>'
                    . __(
                        'This it the beta-environment. All functionality is not guaranteed. Possibly, the web page content will be restored and synchronized with the live site on Monday.',
                        'municipio',
                    )
                    . '</div>'
            ;
        }

        // Css
        echo '
            <style>
                .hosting-enviroment {
                    position: fixed;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    padding: 10px;
                    color: #fff;
                    z-index: 99;
                    text-align: center;
                }
                .hosting-enviroment.hosting-red {
                    background-color: #e3000f;
                }
                .hosting-enviroment.hosting-yellow {
                    background-color: gold;
                    color: #000;
                }
            </style>
        ';
    }

    public function isLocal()
    {
        return isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1' && !isset($_SERVER['HTTP_X_VARNISH']);
    }

    public function isTest()
    {
        return strpos($_SERVER['HTTP_HOST'], 'test.') > -1;
    }

    public function isBeta()
    {
        return strpos($_SERVER['HTTP_HOST'], 'beta.') > -1;
    }
}
