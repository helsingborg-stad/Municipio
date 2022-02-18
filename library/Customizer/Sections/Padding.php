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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'main_content_padding',
            'label'       => esc_html__('Amount of padding around columns.', 'municipio'),
            'description' => esc_html__('Padding will be applied in 8px increments.'),
            'section'     => self::SECTION_ID,
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                'type' => 'controller'
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'organism_grid_gap',
            'label'       => esc_html__('Amount of padding in grids.', 'municipio'),
            'description' => esc_html__('Padding will be applied in 8px increments.'),
            'section'     => self::SECTION_ID,
            'default'     => 4,
            'transport' => 'auto',
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output'      => [
                'element'   => ':root',
                'property'  => '--o-grid-gap',
                'units'     => ''
            ],
        ]);
    }
}
