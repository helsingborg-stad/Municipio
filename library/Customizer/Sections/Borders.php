<?php

namespace Municipio\Customizer\Sections;

class Borders
{
    public const SECTION_ID = "municipio_customizer_section_border";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Borders', 'municipio'),
            'description' => esc_html__('Adjust general borders', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_divider',
            'label'       => esc_html__('Divider', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--border-width-divider',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_highlight',
            'label'       => esc_html__('Highlight', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 4,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--border-width-hightlight',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_card',
            'label'       => esc_html__('Card', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--border-width-card',
                    'units'     => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_outline',
            'label'       => esc_html__('Outline', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--border-width-outline',
                    'units'     => 'px'
                ]
            ],
        ]);


        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_button',
            'label'       => esc_html__('Button', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 2,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--border-width-button',
                'units'     => 'px'
            ],
        ]);


        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'border_width_input',
            'label'       => esc_html__('Input', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--border-width-input',
                'units'     => 'px'
            ],
        ]);
    }
}
