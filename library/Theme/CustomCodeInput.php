<?php

namespace Municipio\Theme;

class CustomCodeInput
{
    public function __construct()
    {
        if (!is_admin()) {
            add_action('wp_head', array($this, 'printCssCode'), 999);
            add_action('wp_footer', array($this, 'printJsCode'), 999);
            add_action('wp_head', array($this, 'headerScripts'), 999);
            add_action('wp_footer', array($this, 'footerScripts'), 999);
        }
    }

    /**
     * Print Css styling from local editor
     * @return void
     */
    public function printCssCode()
    {
        if (!function_exists('get_field')) {
            return;
        }

        $customCss = get_field('custom_css_input', 'option');

        if (empty($customCss)) {
            return;
        }

        echo '<style>' . $customCss . '</style>';
    }

    /**
     * [printJsCode description]
     * @return [type] [description]
     */
    public function printJsCode()
    {
        if (!function_exists('get_field')) {
            return;
        }

        $customJs = get_field('custom_js_input', 'option');

        if (empty($customJs)) {
            return;
        }

        echo '<script>' . $customJs . '</script>';
    }

    public function headerScripts()
    {
        if (get_field('custom_js_tags', 'options') && !empty(get_field('custom_js_tags', 'options'))) {
            foreach (get_field('custom_js_tags', 'options') as $tag) {
                if ($tag['custom_js_tags_disabled'] || $tag['custom_js_tags_location'] !== 'head') {
                    continue;
                }
                echo $tag['custom_js_tags_input'];
            }
        }
    }

    public function footerScripts()
    {
        if (get_field('custom_js_tags', 'options') && !empty(get_field('custom_js_tags', 'options'))) {
            foreach (get_field('custom_js_tags', 'options') as $tag) {
                if ($tag['custom_js_tags_disabled'] || $tag['custom_js_tags_location'] !== 'footer') {
                    continue;
                }
                echo $tag['custom_js_tags_input'];
            }
        }
    }
}
