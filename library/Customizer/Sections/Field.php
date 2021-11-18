<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Field
{
    public const SECTION_ID = "municipio_customizer_section_component_field";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Field', 'municipio'),
            'description' => esc_html__('Field appeance settings.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        /**
         * Border
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'field_border_width',
                'label'       => esc_html__('Width', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 0,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 8,
                    'step' => 1,
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-field-border-width',
                    'units'     => 'px'
                ],
            ],
            [
                'type'        => 'color',
                'settings'    => 'field_border_color',
                'label'       => esc_html__('Color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => Kirki::get_option('color_palette_monotone')['light'] ?? '#eee',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-field-border-color'
                ]
            ],
            [
                'type'        => 'select',
                'settings'    => 'field_border_style',
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
                    'element'   => ':root',
                    'property'  => '--c-field-border-style'
                ]
            ]
        ], ['label' => esc_html__('Tailor border apperance', 'municipio'), 'settings' => 'collection_border_active']);

        /**
         * Background
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'field_background_color',
            'label'       => esc_html__('Background color', 'municipio'),
            'description' => esc_html__('Choose a background color for the field component.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => Kirki::get_option('color_palette_monotone')['lightest'] ?? '#eee',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-field-focus-color'
            ]
        ]);

        /**
         * Focus
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'field_focus_color',
            'label'       => esc_html__('Focus color', 'municipio'),
            'description' => esc_html__('Choose a focus color for the field component.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => Kirki::get_option('color_palette_primary')['base'] ?? '#000',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-field-focus-color'
            ]
        ]);

        /**
         * Font color
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'field_color',
            'label'       => esc_html__('Custom field font color', 'municipio'),
            'description' => esc_html__('Choose a font color for the field component.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => Kirki::get_option('color_palette_primary')['base'] ?? '#000',
            'output'      => [
                'element'   => ':root',
                'property'  => '--c-field-color'
            ]
        ]);

        /**
         * Radius
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'field_border_radius',
                'label'       => esc_html__('Border radius', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 0,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 12,
                    'step' => 2,
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-field-border-radius',
                    'unit'      => 'px'
                ],
            ]
        ]);
    }
}
