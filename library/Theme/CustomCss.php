<?php

namespace Municipio\Theme;

class CustomCss
{
    public function __construct()
    {
        if (!is_admin()) {
            add_action('wp_head', array($this, 'printCssCode'),999);
        }
    }

    /**
     * Print Css styling from local editor
     * @return void
     */
    public function printCssCode()
    {
        if (function_exists('get_field')) {
            $custom_css = get_field('custom_css_input', 'option');

            if (!empty($custom_css)) {
                echo '<style>' . $custom_css . '</style>';
            }
        }
    }
}
