<?php

namespace Municipio\Customizer\Sections;

class Divider
{
    public const SECTION_ID = "municipio_customizer_section_divider";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Divider', 'municipio'),
            'description' => esc_html__('Divider settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'divider_thickness',
            'label'       => esc_html__('Divider thickness', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-divider-thickness',
                    'units'      => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'divider_border_style',
            'label'       => esc_html__('Border style', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport'   => 'refresh',
            'default'     => 'solid',
            'choices'     => [
                'solid'  => __('Solid', 'municipio'),
                'dotted'  => __('Dotted', 'municipio'),
                'dashed' => __('Dashed', 'municipio')
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'style',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'divider_colors',
            'label'       => esc_html__('Colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => true,
            'choices'     => [
                'divider'    => esc_html__('Divider', 'municipio'),
            ],
            'default'     => [
                'divider'     => '#707070',
            ],
            'output' => [
                [
                    'choice'    => 'divider',
                    'element'   => ':root',
                    'property'  => '--c-divider-color-divider',
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'divider_custom_font',
            'label'       => esc_html__('Customize text color', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport'   => 'refresh',
            'default'     => false,
            'choices'     => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'customFont',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'divider_color_text',
            'label'       => esc_html__('Text color', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => true,
            'default'     => '',
            'active_callback' => [
                [
                    'setting'  => 'divider_custom_font',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'output' => [
                [
                    'choice'    => 'text',
                    'element'   => ':root',
                    'property'  => '--c-divider-color-text',
                ],
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'divider_title_alignment',
            'label'       => esc_html__('Text alignment', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport'   => 'refresh',
            'default'     => 'center',
            'choices'     => [
                'left'  => __('Left', 'municipio'),
                'center'  => __('Center', 'municipio'),
                'right' => __('Right', 'municipio')
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'align',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'divider_title_frame',
            'label'       => esc_html__('Wrap title in frame', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport'   => 'refresh',
            'default'     => true,
            'choices'     => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'frame',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'divider_frame_colors',
            'label'       => esc_html__('Frame colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => true,
            'active_callback' => [
                [
                    'setting'  => 'divider_title_frame',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'choices'     => [
                'background'    => esc_html__('Background color', 'municipio'),
                'border'    => esc_html__('Border color', 'municipio'),
            ],
            'default'     => [
                'background' => '#fff',
                'border'     => '#707070',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--c-divider-title-background',
                ],
                [
                    'choice'    => 'border',
                    'element'   => ':root',
                    'property'  => '--c-divider-title-border-color'
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'divider_title_border_radius',
            'label'       => esc_html__('Frame border radius', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 0,
            'active_callback' => [
                [
                    'setting'  => 'divider_title_frame',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-divider-title-border-radius',
                    'units'      => 'px'
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'divider_title_border_width',
            'label'       => esc_html__('Frame border width', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 1,
            'active_callback' => [
                [
                    'setting'  => 'divider_title_frame',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-divider-title-border-width',
                    'units'      => 'px'
                ]
            ],
        ]);
    }
}
