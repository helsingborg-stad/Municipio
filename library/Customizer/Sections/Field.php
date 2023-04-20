<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiSwatches as KirkiSwatches;

class Field
{
    public function __construct(string $sectionID)
    {
         \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'field_appearance_type',
          'label'       => esc_html__('Appearance', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__('Predefined appearance', 'municipio'),
            'custom' => esc_html__('Custom appearance', 'municipio'),
          ],
        ]);

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
                    'component.form',
                    'component.filterselect'
                  ]
                ]
            ],
            'active_callback'  => [
                [
                    'setting'  => 'field_appearance_type',
                    'operator' => '===',
                    'value'    => 'default',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'multicolor',
          'settings'    => 'field_custom_colors',
          'label'       => esc_html__('Custom colors', 'municipio'),
          'section'     => $sectionID,
          'priority'    => 10,
          'transport' => 'auto',
          'choices'     => [
            'background'    => esc_html__('Background color', 'municipio'),
            'border-color'  => esc_html__('Border color', 'municipio'),
          ],
          'default' => [
            'background'        => '#f5f5f5',
            'border-color'      => '#a3a3a3',
          ],
          'palettes' => KirkiSwatches::getColors(),
          'output' => [
            [
                'choice'    => 'background',
                'element'   => ':root',
                'property'  => '--c-field-background-color'
            ],
            [
                'choice'    => 'border-color',
                'element'   => ':root',
                'property'  => '--c-field-border-color'
            ],
          ],
          'active_callback'  => [
                [
                    'setting'  => 'field_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'field_custom_radius',
            'label'       => esc_html__('Select radius', 'municipio'),
            'section'     => $sectionID,
            'default'     => '0',
            'choices'     => [
                '0' => '0',
                '0.25' => '2',
                '0.5' => '4',
                '1' => '8',
                '1.5' => '12',
                '3' => '24',
            ],
            'output' => [
                [
                'element' => ':root',
                'property'  => '--c-field-border-radius',
                'value' => '$'
                ]
            ],
            'active_callback'  => [
                [
                    'setting'  => 'field_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                ]
            ],
        ]);
    }
}
