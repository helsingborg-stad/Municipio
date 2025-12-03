<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Padding
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'main_content_padding',
            'label'       => esc_html__('Amount of padding around columns.', 'municipio'),
            'description' => esc_html__('Padding will be applied in 8px increments.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output'      => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'organism_grid_gap',
            'label'       => esc_html__('Amount of padding in grids.', 'municipio'),
            'description' => esc_html__('Padding will be applied in 8px increments.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 4,
            'transport'   => 'auto',
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 0.5,
            ],
            'output'      => [
                [
                    'element'  => ':root',
                    'property' => '--o-grid-gap'
                ]
            ]
        ]);

        KirkiField::addField([
            'type'        => 'radio_buttonset',
            'settings'    => 'flat_ui_design',
            'label'       => esc_html__('Air in cards', 'municipio'),
            'description' => esc_html__('Enable to remove space between content and the edge of cards.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''     => esc_html__('Apply air', 'municipio'),
                'flat' => esc_html__('Remove air', 'municipio')
            ],
            'output'      => [
                [
                  'type'    => 'modifier',
                  'context' => [
                    'component.card',
                    'component.paper',
                    'component.slider'
                  ]
                ]
            ],
        ]);
    }
}
