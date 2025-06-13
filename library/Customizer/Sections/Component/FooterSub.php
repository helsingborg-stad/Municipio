<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class FooterSub
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'footer_subfooter_colors',
            'label'     => esc_html__('Colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'background' => esc_html__('Background', 'municipio'),
                'text'       => esc_html__('Base Contrasting', 'municipio'),
                'separator'  => esc_html__('Text separator', 'municipio'),
            ],
            'default'   => [
                'background' => '#fff',
                'text'       => '#000',
                'separator'  => '#A3A3A3',
            ],
            'output'    => [
                [
                    'choice'   => 'background',
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-color-background',
                ],
                [
                    'choice'   => 'text',
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-color-text',
                ],
                [
                    'choice'   => 'separator',
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-color-separator',
                ]
            ]
        ]);

        KirkiField::addField([
            'type'      => 'select',
            'settings'  => 'footer_subfooter_logotype',
            'label'     => esc_html__('Logotype', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'refresh',
            'default'   => 'hide',
            'choices'   => [
                'hide'     => __('None', 'municipio'),
                'standard' => __('Primary', 'municipio'),
                'negative' => __('Secondary', 'municipio'),
                'custom'   => __('Custom', 'municipio')
            ],
            'output'    => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'            => 'upload',
            'settings'        => 'footer_subfooter_custom_logotype',
            'label'           => esc_html__('Upload SVG logo', 'municipio'),
            'description'     => 'Upload a custom .svg file to use as logo.',
            'section'         => $sectionID,
            'priority'        => 10,
            'transport'       => 'refresh',
            'active_callback' => [
                [
                    'setting'  => 'footer_subfooter_logotype',
                    'operator' => '==',
                    'value'    => 'custom',
                ]
            ],
            'output'          => [
                ['type' => 'controller']
            ]
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'footer_subfooter_height_logotype',
            'label'     => esc_html__('Logotype height', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 6,
            'choices'   => [
                'min'  => 3,
                'max'  => 24,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-height-logotype',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'footer_subfooter_padding',
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
                    'property' => '--c-footer-subfooter-padding',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'select',
            'settings'  => 'footer_subfooter_flex_direction',
            'label'     => esc_html__('Direction', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'refresh',
            'default'   => 'row',
            'choices'   => [
                'row'    => __('Horizontal', 'municipio'),
                'column' => __('Vertical', 'municipio')
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-flex-direction',
                ],
                [
                    'type'    => 'component_data',
                    'dataKey' => 'subfooter.flexDirection',
                    'context' => [
                        [
                            'context'  => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'select',
            'settings'  => 'footer_subfooter_alignment',
            'label'     => esc_html__('Content alignment', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'refresh',
            'default'   => 'center',
            'choices'   => [
                'flex-start' => __('Left', 'municipio'),
                'center'     => __('Center', 'municipio'),
                'flex-end'   => __('Right', 'municipio')
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--c-footer-subfooter-alignment',
                ],
                [
                    'type'    => 'component_data',
                    'dataKey' => 'subfooter.alignment',
                    'context' => [
                        [
                            'context'  => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'repeater',
            'settings'  => 'footer_subfooter_content',
            'label'     => esc_html__('Content', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'fields'    => [
                'title'   => [
                    'type'    => 'text',
                    'label'   => esc_html__('Title', 'muncipio'),
                    'default' => '',
                ],
                'content' => [
                    'type'    => 'text',
                    'label'   => esc_html__('Text', 'muncipio'),
                    'default' => '',
                ],
                'link'    => [
                    'type'    => 'url',
                    'label'   => esc_html__('Link', 'muncipio'),
                    'default' => '',
                ],
            ],
            'output'    => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'subfooter.content',
                    'context' => [
                        [
                            'context'  => 'component.footer',
                            'operator' => '==',
                        ],
                    ]
                ]
            ]
        ]);
    }
}
