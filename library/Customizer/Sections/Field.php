<?php

namespace Municipio\Customizer\Sections;

class Field
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'field_style_settings',
            'label'       => esc_html__('Field style', 'municipio'),
            'description' => esc_html__('Which styling the input field use.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''   => esc_html__('Default', 'municipio'),
                'rounded' => esc_html__('Rounded', 'municipio')
            ],
            'output' => [
                [
                  'type' => 'modifier',
                  'context' => [
                    'component.field',
                    'component.select',
                    'component.form'
                  ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'field_style_rounded_border_setting',
            'label'       => esc_html__('Border', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''   => esc_html__('No border', 'municipio'),
                'rounded-border' => esc_html__('Border', 'municipio')
            ],
            'output' => [
                [
                  'type' => 'modifier',
                  'context' => [
                    'component.field',
                    'component.select',
                  ]
                ]
            ],
            'active_callback'  => [
                [
                    'setting'  => 'field_style_settings',
                    'operator' => '===',
                    'value'    => 'rounded',
                ]
            ],
        ]);
    }
}
