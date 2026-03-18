<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Colors
{
    public function __construct($sectionID)
    {

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_primary',
            'label'     => esc_html__('Primary colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#ae0b05',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-primary',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-primary-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_secondary',
            'label'     => esc_html__('Secondary colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#ec6701',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-secondary',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-secondary-contrasting',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_background',
            'label'     => esc_html__('Background colors', 'municipio'),
            'section'   => $sectionID,
            'alpha'     => true,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'background'    => esc_html__('Background', 'municipio'),
            ],
            'default'   => [
                'background'    => '#f5f5f5',
            ],
            'output'    => [
                [
                    'choice'   => 'background',
                    'element'  => ':root',
                    'property' => '--color-background',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_card',
            'label'     => esc_html__('Surface colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'background' => esc_html__('Background', 'municipio'),
            ],
            'default'   => [
                'background' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'background',
                    'element'  => ':root',
                    'property' => '--color-background-card',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_text',
            'label'     => esc_html__('Text colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'      => esc_html__('Base', 'municipio'),
            ],
            'default'   => [
                'base'      => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-base',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_alpha',
            'label'     => esc_html__('Alpha colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'alpha'     => true,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Base Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => 'rgba(0,0,0,0.55)',
                'contrasting' => 'rgb(255,255,255)',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-alpha',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-alpha-contrasting',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_success',
            'label'     => esc_html__('Success colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#91d736',
                'contrasting' => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-success',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-success-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_danger',
            'label'     => esc_html__('Danger colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#d73740',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-danger',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-danger-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_warning',
            'label'     => esc_html__('Warning colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#efbb21',
                'contrasting' => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-warning',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-warning-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_info',
            'label'     => esc_html__('Info colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => '#3d3d3d',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-info',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-info-contrasting',
                ],
            ]
        ]);
    }
}
