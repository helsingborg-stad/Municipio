<?php

namespace Municipio\Customizer\Sections;

class Padding
{
    public const SECTION_ID = "municipio_customizer_section_padding";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Padding', 'municipio'),
            'description' => esc_html__('Padding settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
