<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class FooterMain
{
    public function __construct(string $sectionID)
    {
        /**
         * Main footer settings
         */
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'footer_style',
            'label'       => esc_html__('Footer style', 'municipio'),
            'description' => esc_html__('Which style of footer to use.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'basic',
            'choices'     => [
                'basic'   => esc_html__('Basic', 'municipio'),
                'columns' => esc_html__('Columns', 'municipio'),
            ],
            'output'      => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'footer_padding',
            'label'     => esc_html__('Padding', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 3,
            'choices'   => [
                'min'  => 1,
                'max'  => 24,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-padding',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'select',
            'settings'  => 'footer_logotype',
            'label'     => esc_html__('Logotype', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'refresh',
            'default'   => 'negative',
            'choices'   => [
                'hide'     => __('None', 'municipio'),
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
            ],
            'output'    => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'footer_height_logotype',
            'label'     => esc_html__('Logotype height', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 6,
            'choices'   => [
                'min'  => 3,
                'max'  => 12,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-height-logotype',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'footer_logotype_alignment',
            'label'       => esc_html__('Logotype alignment', 'municipio'),
            'description' => esc_html__('How to align the logo in the footer.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'align-left',
            'choices'     => array(
                'align-left'   => __('Left', 'municipio'),
                'align-center' => __('Center', 'municipio'),
                'align-right'  => __('Right', 'municipio'),
            ),
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => ['footer.logotype'],
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'select',
            'settings'        => 'footer_text_alignment',
            'label'           => esc_html__('Text alignment', 'municipio'),
            'description'     => esc_html__('How to align the text in the columns.', 'municipio'),
            'section'         => $sectionID,
            'default'         => 'u-text-align--left',
            'choices'         => array(
                'u-text-align--left'   => __('Left', 'municipio'),
                'u-text-align--center' => __('Center', 'municipio'),
                'u-text-align--right'  => __('Right', 'municipio'),
            ),
            'active_callback' => [
                [
                    'setting'  => 'footer_style',
                    'operator' => '==',
                    'value'    => 'columns',
                ]
            ],
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'pre_footer_text_alignment',
            'label'       => esc_html__('Pre-footer Text alignment', 'municipio'),
            'description' => esc_html__('How to align the text in the pre-footer.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'u-text-align--left',
            'choices'     => array(
                'u-text-align--left'   => __('Left', 'municipio'),
                'u-text-align--center' => __('Center', 'municipio'),
                'u-text-align--right'  => __('Right', 'municipio'),
            ),
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'preFooterTextAlignment',
                    'context' => [
                        [
                            'context'  => 'component.footer',
                            'operator' => '==',
                        ],
                    ],
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'checkbox_switch',
            'settings'    => 'footer_header_border',
            'label'       => esc_html__('Footer header border', 'municipio'),
            'description' => esc_html__('Add a bottom border for the footer header', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'off',
            'choices'     => [
                'on'  => esc_html__('Enable', 'kirki'),
                'off' => esc_html__('Disable', 'kirki'),
            ],
            'output'      => [
                [
                    'type'      => 'modifier',
                    'context'   => ['component.footer'],
                    'value_map' => [
                        true => 'header-border'
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'            => 'slider',
            'settings'        => 'footer_header_border_size',
            'label'           => esc_html__('Footer header border size', 'municipio'),
            'description'     => esc_html__('The size of the footer header\'s bottom border', 'municipio'),
            'section'         => $sectionID,
            'transport'       => 'auto',
            'default'         => 1,
            'choices'         => [
                'min'  => 1,
                'max'  => 8,
                'step' => 1,
            ],
            'active_callback' => [
                [
                    'setting'  => 'footer_header_border',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'output'          => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-header-border-size',
                    'units'    => 'px',
                ]
            ]
        ]);

        KirkiField::addField([
            'type'            => 'color',
            'settings'        => 'footer_header_border_color',
            'label'           => esc_html__('Footer header border color', 'municipio'),
            'description'     => esc_html__('The color of the footer header\'s bottom border', 'municipio'),
            'section'         => $sectionID,
            'transport'       => 'auto',
            'default'         => '#000',
            'active_callback' => [
                [
                    'setting'  => 'footer_header_border',
                    'operator' => '==',
                    'value'    => true,
                ]
            ],
            'output'          => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-header-border-color',
                ]
            ]
        ]);

        KirkiField::addField([
            'type'            => 'slider',
            'settings'        => 'footer_columns',
            'label'           => esc_html__('Number of columns to display', 'municipio'),
            'description'     => esc_html__('How many columns that the footer should be divided in.', 'municipio'),
            'section'         => $sectionID,
            'default'         => 1,
            'choices'         => [
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
            'output'          => [
                [
                    'type'      => 'controller',
                    'as_object' => true,
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'color',
            'settings'  => 'footer_color_text',
            'label'     => esc_html__('Text color', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'default'   => '#000',
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-color-text',
                ]
            ]
        ]);

        KirkiField::addField([
            'type'        => 'background',
            'settings'    => 'footer_background',
            'label'       => esc_html__('Footer background', 'municipio'),
            'description' => esc_html__('Background settings for the footer.', 'municipio'),
            'section'     => $sectionID,
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
    }
}
