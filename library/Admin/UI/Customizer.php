<?php

namespace Municipio\Admin\UI;

/**
 * Class Customizer
 * @package Municipio\Admin\UI
 */
class Customizer
{
    /**
     * Customizer constructor.
     */
    public function __construct()
    {
        add_action('init', function () {
            if (is_customize_preview() && !class_exists('ACFCustomizer\Core\Core')) {
                $this->throwNotice();
            }
        });
    }

    /**
     * Create a error message with installation instructions
     *
     * @return void
     */
    public function throwNotice()
    {
        wp_die(
            __("<h1>Plugin install required </h1> <p>To use the customizer with municipio its required to use ACFCustomizer plugin. Please install this plugin by cloning <a href='https://www.awesomeacf.com/extension/customizer/'>ACF Customizer</a>.</p>"),
            __("Plugin install required")
        );
    }
}