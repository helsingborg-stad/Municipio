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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_animation',
            'label'       => esc_html__('Choose animation type', 'municipio'),
            'description' => esc_html__('Choose an animation for your hero', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('No animation', 'municipio'),
                'animation-type-kenny' => esc_html__('Ken Burns', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => [
                        [
                            'context' => 'sidebar.slider-area.animation-item',
                            'operator' => '=='
                        ],   
                    ],
                ],
            ],
        ]);
    }
}
