<?php

namespace Municipio\Customizer\Sections;

class General
{
    public const SECTION_ID = "municipio_customizer_section_general";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('General', 'municipio'),
            'description' => esc_html__('General settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
