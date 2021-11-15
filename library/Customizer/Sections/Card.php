<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Card
{
    public const SECTION_ID = "municipio_customizer_section_component_card";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Card', 'municipio'),
            'description' => esc_html__('Card settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'card_background',
            'label'       => esc_html__('Custom card background color', 'municipio'),
            'description' => esc_html__('Choose a background color for the cards.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '#ffffff',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-card-background-color'
            ]
        ]);

        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'card_color',
            'label'       => esc_html__('Custom card font color', 'municipio'),
            'description' => esc_html__('Choose a font color for the cards.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '#000000',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-card-color'
            ]
        ]);
    }
}
