<?php

namespace Municipio\Customizer\Sections;

class Quicklinks
{
    public const SECTION_ID = "municipio_customizer_section_quicklinks";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Quicklinks', 'municipio'),
            'description' => esc_html__('Quicklinks settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
