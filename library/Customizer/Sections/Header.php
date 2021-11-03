<?php

namespace Municipio\Customizer\Sections;

class Header
{
    public const SECTION_ID = "municipio_customizer_section_header";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Header', 'municipio'),
            'description' => esc_html__('Header settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
