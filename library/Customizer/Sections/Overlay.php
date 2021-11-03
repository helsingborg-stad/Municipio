<?php

namespace Municipio\Customizer\Sections;

class Overlay
{
    public const SECTION_ID = "municipio_customizer_section_overlay";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Overlay', 'municipio'),
            'description' => esc_html__('Overlay settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
