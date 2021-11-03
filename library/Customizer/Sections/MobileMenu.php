<?php

namespace Municipio\Customizer\Sections;

class MobileMenu
{
    public const SECTION_ID = "municipio_customizer_section_mobilemenu";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Mobile menu', 'municipio'),
            'description' => esc_html__('Mobile menu settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
