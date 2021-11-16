<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Field
{
    public const SECTION_ID = "municipio_customizer_section_component_field";

    public function __construct($panelID)
    {
        /*Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Card & Paper', 'municipio'),
            'description' => esc_html__('Card & paper settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));*/

        /**
         *  Field props
         * 
         *  - Radius
         *  - Background Color (contrast)
         *  - Background color:active
         *  - Font color 
         *  - Placeholder color 
         *  - Outline color
         *  - Outline width
         *  - Outline style (?)
         *  
         *  - Validation Background color
         *  - Validation Font color
         * 
         */



         if(!$customize->shadow_enabled) {
            echo '<style>:root{--shadow: none;}</style>'; 
         }
    }
}
