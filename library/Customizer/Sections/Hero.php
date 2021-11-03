<?php

namespace Municipio\Customizer\Sections;

class Hero
{
    public const SECTION_ID = "municipio_customizer_section_hero";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Hero', 'municipio'),
            'description' => esc_html__('Hero settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));
    }
}
