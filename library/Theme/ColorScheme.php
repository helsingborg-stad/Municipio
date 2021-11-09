<?php

namespace Municipio\Theme;

/**
 * Class ColorScheme
 * @package Municipio\Theme
 */
class ColorScheme
{
    public function __construct()
    {
        add_action(
            'acf/input/admin_enqueue_scripts', 
            array(
                $this, 
                'colorPickerDefaultPaletteJs'
            ),
            10
        );
    }

    /**
     * Localize theme colors to set color picker default colors
     * @return void
     */
    public function colorPickerDefaultPaletteJs() {

        //Load js
        wp_register_script(
            'colorpicker-js', 
            get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/color-picker.js')
        );
        wp_enqueue_script('colorpicker-js');

        //Add colors
        wp_localize_script('colorpicker-js', 'themeColorPalette', [
            'colors' => \Municipio\Helper\Color::getPalette(),
        ]);

    }
}