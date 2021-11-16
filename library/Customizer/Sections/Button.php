<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Button
{
    public const SECTION_ID = "municipio_customizer_section_component_button";

    public function __construct($panelID)
    {
        /*Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Card & Paper', 'municipio'),
            'description' => esc_html__('Card & paper settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));*/

        /**
         *  Button props (small, medium [Ta bort large button])
         * 
         *  - Radius small
         *  - Radius medium
         * 
         *  - Primary
         *      - Color
         *      - Background
         *  - Secondary
         *     - Color
         *     - Background
         */
    }
}
