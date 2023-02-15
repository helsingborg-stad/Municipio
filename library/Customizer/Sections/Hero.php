<?php

namespace Municipio\Customizer\Sections;

class Hero
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_animation',
            'label'       => esc_html__('Choose animation type', 'municipio'),
            'description' => esc_html__('Choose an animation for your hero', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                '' => esc_html__('No animation', 'municipio'),
                'animation-type-kenny' => esc_html__('Ken Burns', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'animation',
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
