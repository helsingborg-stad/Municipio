<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiConditional as KirkiConditional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Collection
{
    public const SECTION_ID = "municipio_customizer_section_component_collection";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Collection', 'municipio'),
            'description' => esc_html__('Collection appeance settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        /**
         * Background
         */
        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'color',
                'settings'    => 'collection_background',
                'label'       => esc_html__('Background color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#eee',
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-background-color'
                    ]
                ]
            ],
            [
                'type'        => 'color',
                'settings'    => 'collection_background_hover',
                'label'       => esc_html__('Background color - Hover', 'municipio'),
                'description' => esc_html__('Background color on hover.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#eee',
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-background-color--hover'
                    ]
                ]
            ]
        ], ['label' => esc_html__('Tailor background color', 'municipio'), 'settings' => 'collection_background_active']);

        /**
         * Font color
         */
        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'collection_color',
            'label'       => esc_html__('Custom collection font color', 'municipio'),
            'description' => esc_html__('Choose a font color for the collection component.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '#000000',
            'output'      => [
                [
                    'element'   => ':root',
                    'property'  => '--c-collection-color'
                ]
            ]
        ]);

        /**
         * Border
         */
        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'collection_border_width',
                'label'       => esc_html__('Width', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 0,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 8,
                    'step' => 1,
                ],
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-border-width',
                        'units'     => 'px'
                    ]
                ],
            ],
            [
                'type'        => 'color',
                'settings'    => 'collection_border_color',
                'label'       => esc_html__('Color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => '#000000',
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-border-color'
                    ]
                ]
            ],
            [
                'type'        => 'select',
                'settings'    => 'collection_border_style',
                'label'       => esc_html__('Style', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 'solid',
                'choices'     => [
                    'dotted'    => esc_html__('Dotted', 'municipio'),
                    'dashed'    => esc_html__('Dashed', 'municipio'),
                    'solid'     => esc_html__('Solid', 'municipio'),
                    'double'    => esc_html__('Double', 'municipio'),
                    'groove'    => esc_html__('Groove', 'municipio'),
                    'ridge'     => esc_html__('Ridge', 'municipio'),
                    'inset'     => esc_html__('Inset', 'municipio'),
                    'outset'    => esc_html__('Outset', 'municipio'),
                    'none'      => esc_html__('None', 'municipio'),
                    'hidden'    => esc_html__('Hidden', 'municipio')
                ],
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-border-style'
                    ]
                ]
            ]
        ], ['label' => esc_html__('Tailor border apperance', 'municipio'), 'settings' => 'collection_border_active']);

        /**
         * Radius
         */
        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'collection_border_radius',
                'label'       => esc_html__('Border radius', 'municipio'),
                'description' => esc_html__('Border radius on wrapped collections are automatically removed if needed.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 0,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 12,
                    'step' => 2,
                ],
                'output'      => [
                    [
                        'element'   => ':root',
                        'property'  => '--c-collection-border-radius',
                        'unit'      => 'px'
                    ]
                ],
            ]
        ]);
    }
}
