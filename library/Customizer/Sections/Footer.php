<?php

namespace Municipio\Customizer\Sections;

class Footer
{
    public const SECTION_ID = "municipio_customizer_section_component_footer_main";
    public const SUBFOOTER_SECTION_ID = "municipio_customizer_section_component_footer_subfooter";
    public const PANEL_ID = "municipio_customizer_panel_component_footer";

    public function __construct($panelID)
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Footer', 'municipio'),
            'description' => esc_html__('Footer settings.', 'municipio'),
            'panel'       => $panelID
        ));

        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Main footer', 'municipio'),
            'description' => esc_html__('Main footer settings.', 'municipio'),
            'panel'       => self::PANEL_ID,
            'priority'    => 160,
        ));

        \Kirki::add_section(self::SUBFOOTER_SECTION_ID, array(
            'title'       => esc_html__('Sub footer', 'municipio'),
            'description' => esc_html__('Sub footer settings.', 'municipio'),
            'panel'       => self::PANEL_ID,
            'priority'    => 160,
        ));

        /**
         * Main footer settings
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'footer_style',
            'label'    => esc_html__('Footer style', 'municipio'),
            'description' => esc_html__('Which style of footer to use.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'basic',
            'choices'     => [
                'basic' => esc_html__('Basic', 'municipio'),
                'columns' => esc_html__('Columns', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_padding',
            'label'       => esc_html__('Padding', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 3,
            'choices'     => [
                'min'  => 1,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-padding',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_logotype',
            'label'       => esc_html__('Logotype', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'refresh',
            'default'     => 'negative',
            'choices'     => [
                'hide'  => __('None', 'municipio'),
                'standard'  => __('Primary', 'municipio'),
                'negative'  => __('Secondary', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller']
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_height_logotype',
            'label'       => esc_html__('Logotype height', 'municipio'),
            'section'     => self::SECTION_ID,
            'transport' => 'auto',
            'default'     => 6,
            'choices'     => [
                'min'  => 3,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-height-logotype',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'footer_logotype_alignment',
            'label'    => esc_html__('Logotype alignment', 'municipio'),
            'description' => esc_html__('How to align the logo in the footer.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'align-left',
            'choices' => array(
                'align-left' => __('Left', 'modularity'),
                'align-center' => __('Center', 'modularity'),
                'align-right' => __('Right', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['footer.logotype'],
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'footer_text_alignment',
            'label'    => esc_html__('Text alignment', 'municipio'),
            'description' => esc_html__('How to align the text in the columns.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'u-text-align--left',
            'choices' => array(
                'u-text-align--left' => __('Left', 'modularity'),
                'u-text-align--center' => __('Center', 'modularity'),
                'u-text-align--right' => __('Right', 'modularity'),
            ),
            'active_callback' => [
                [
                    'setting'  => 'footer_style',
                    'operator' => '==',
                    'value'    => 'columns',
                ]
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);
      
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'select',
            'settings' => 'pre_footer_text_alignment',
            'label'    => esc_html__('Pre-footer Text alignment', 'municipio'),
            'description' => esc_html__('How to align the text in the .', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'u-text-align--left',
            'choices' => array(
                'u-text-align--left' => __('Left', 'modularity'),
                'u-text-align--center' => __('Center', 'modularity'),
                'u-text-align--right' => __('Right', 'modularity'),
            ),
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'preFooterTextAlignment',
                    'context' => [
                        [
                            'context' => 'component.footer',
                            'operator' => '==',
                        ],
                    ],
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'checkbox_switch',
            'settings' => 'pre_footer_border',
            'label'    => esc_html__('Pre-footer border', 'municipio'),
            'description' => esc_html__('Add a bottom border for the pre-footer', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 'off',
            'choices' => [
                'on'  => esc_html__( 'Enable', 'kirki' ),
                'off' => esc_html__( 'Disable', 'kirki' ),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['component.footer'],
                    'value_map' => [
                        true => 'prefooter-border'
                    ]
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'slider',
            'settings' => 'footer_columns',
            'label'    => esc_html__('Number of columns to display', 'municipio'),
            'description' => esc_html__('How many columns that the footer should be divided in.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'  => 1,
            'choices'     => [
                'min'  => 1,
                'max'  => 6,
                'step' => 1,
            ],
            'active_callback' => [
                [
                    'setting'  => 'footer_style',
                    'operator' => '==',
                    'value'    => 'columns',
                ]
            ],
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'footer_color_text',
            'label'       => esc_html__('Text color', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'default'     => '#000',
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-color-text',
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type' => 'background',
            'settings' => 'footer_background',
            'label'    => esc_html__('Footer background', 'municipio'),
            'description' => esc_html__('Background settings for the footer.', 'municipio'),
            'section'  => self::SECTION_ID,
            'default'     => [
                'background-color'      => 'var(--color-white,#fff)',
                'background-image'      => '',
                'background-repeat'     => 'repeat',
                'background-position'   => 'center center',
                'background-size'       => 'cover',
                'background-attachment' => 'scroll',
            ],
            'transport'   => 'auto',
            'output'      => [
                [
                    'element' => '.c-footer',
                ],
            ],
        ]);

        /**
         * Sub footer settings
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'footer_subfooter_colors',
            'label'       => esc_html__('Colors', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => true,
            'choices'     => [
                'background'    => esc_html__('Background', 'municipio'),
                'text'    => esc_html__('Kontrastfärg', 'municipio'),
                'separator'    => esc_html__('Text separator', 'municipio'),
            ],
            'default'     => [
                'background'    => '#fff',
                'text'          => '#000',
                'separator'     => '#A3A3A3',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-background',
                ],
                [
                    'choice'    => 'text',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-text',
                ],
                [
                    'choice'    => 'separator',
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-color-separator',
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_subfooter_logotype',
            'label'       => esc_html__('Logotype', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport' => 'refresh',
            'default'     => 'hide',
            'choices'     => [
                'hide'  => __('None', 'municipio'),
                'standard'  => __('Primary', 'municipio'),
                'negative'  => __('Secondary', 'municipio'),
                'custom'  => __('Custom', 'municipio')
            ],
            'output' => [
                ['type' => 'controller']
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'upload',
            'settings'    => 'footer_subfooter_custom_logotype',
            'label'       => esc_html__('Upload SVG logo', 'municipio'),
            'description' => 'Upload a custom .svg file to use as logo.',
            'section'     => self::SUBFOOTER_SECTION_ID,
            'priority'    => 10,
            'transport'   => 'refresh',
            'active_callback' => [
                [
                    'setting'  => 'footer_subfooter_logotype',
                    'operator' => '==',
                    'value'    => 'custom',
                ]
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_subfooter_height_logotype',
            'label'       => esc_html__('Logotype height', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport' => 'auto',
            'default'     => 6,
            'choices'     => [
                'min'  => 3,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-height-logotype',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'footer_subfooter_padding',
            'label'       => esc_html__('Padding', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport' => 'auto',
            'default'     => 3,
            'choices'     => [
                'min'  => 1,
                'max'  => 12,
                'step' => 1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-padding',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_subfooter_flex_direction',
            'label'       => esc_html__('Direction', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport' => 'refresh',
            'default'     => 'row',
            'choices'     => [
                'row'  => __('Horizontal', 'municipio'),
                'column'  => __('Vertical', 'municipio')
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-flex-direction',
                ],
                [
                    'type' => 'component_data',
                    'dataKey' => 'subfooter.flexDirection',
                    'context' => [
                        [
                            'context' => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
      
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'footer_subfooter_alignment',
            'label'       => esc_html__('Content alignment', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport'   => 'refresh',
            'default'     => 'center',
            'choices'     => [
                'flex-start'  => __('Left', 'municipio'),
                'center'  => __('Center', 'municipio'),
                'flex-end' => __('Right', 'municipio')
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--c-footer-subfooter-alignment',
                ],
                [
                    'type' => 'component_data',
                    'dataKey' => 'subfooter.alignment',
                    'context' => [
                        [
                            'context' => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
      
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'repeater',
            'settings'    => 'footer_subfooter_content',
            'label'       => esc_html__('Content', 'municipio'),
            'section'     => self::SUBFOOTER_SECTION_ID,
            'transport' => 'auto',
            'fields'   => [
                'title'   => [
                    'type'        => 'text',
                    'label'       => esc_html__( 'Title', 'muncipio' ),
                    'default'     => '',
                ],
                'content'   => [
                    'type'        => 'text',
                    'label'       => esc_html__( 'Text', 'muncipio' ),
                    'default'     => '',
                ],
                'link'   => [
                    'type'        => 'url',
                    'label'       => esc_html__( 'Link', 'muncipio' ),
                    'default'     => '',
                ],
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'subfooter.content',
                    'context' => [
                        [
                            'context' => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ]
        ]);
    }
}
