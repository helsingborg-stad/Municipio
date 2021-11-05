<?php

namespace Municipio\Theme;

/**
 * Class ColorScheme
 * @package Municipio\Theme
 */
class ColorScheme
{

    private $colorTargetKeys = [

        //Primary
        'field_60361bcb76325',
        'field_60364d06dc120',
        'field_603fba043ab30',

        //Secondary
        'field_603fba3ffa851',
        'field_603fbb7ad4ccf',
        'field_603fbbef1e2f8',

        //Tertiary
        'field_608c0e753ef05',
        'field_608c0e813ef06',
        'field_608c0e8c3ef07'
    ];

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

        //Fetch color scheme
        $colors = (array) apply_filters(
            'Municipio/Theme/ColorPickerPalette', 
            [],//$this->getColorPalette()
        );

        //Add colors
        wp_localize_script('colorpicker-js', 'themeColorPalette', [
            'colors' => $colors,
        ]);

    }

    /**
     * Get color palette from theme options
     *
     * @return array
     */
    public function getColorPalette() {

        //Get & flatten theme mods 
        $themeMods = \Municipio\Helper\CustomizeGet::get(); 

        //Get target keys
        $colorTargetKeys = $this->colorTargetKeys; 

        //Get hex values unique
        $colors =   array_unique(
                        array_filter($themeMods, function($value, $key) use($colorTargetKeys) {

                            //Only get those defined
                            if(!in_array($key, $colorTargetKeys)) {
                                return false; 
                            }

                            //Enshure this is a color
                            return strpos($value, "#") === 0;

                        }, ARRAY_FILTER_USE_BOTH)
                    );

        //Reset & return
        return array_values($colors); 
    }
}