<?php

namespace Municipio\Customizer\Sections;

class Radius
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'radius_xs',
            'label'       => esc_html__('Extra small', 'municipio'),
            'section'     => $sectionID,
            'transport' => 'auto',
            'default'     => 2,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--radius-xs',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'radius_sm',
            'label'       => esc_html__('Small', 'municipio'),
            'section'     => $sectionID,
            'transport' => 'auto',
            'default'     => 4,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--radius-sm',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'radius_md',
            'label'       => esc_html__('Medium', 'municipio'),
            'section'     => $sectionID,
            'transport' => 'auto',
            'default'     => 8,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--radius-md',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'radius_lg',
            'label'       => esc_html__('Large', 'municipio'),
            'section'     => $sectionID,
            'transport' => 'auto',
            'default'     => 12,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--radius-lg',
                    'units'     => 'px'
                ]
            ],
        ]);
    }
}
