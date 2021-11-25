<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Button
{
    public const SECTION_ID = "municipio_customizer_section_component_button";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Buttons', 'municipio'),
            'description' => esc_html__('Settings for buttons.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        /**
         * Radius
         */
        /*KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'button_border_radius_sm',
                'label'       => esc_html__('Radius small', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 4,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 12,
                    'step' => 2,
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-border-radius-sm',
                    'unit'      => 'px'
                ],
            ],
            [
                'type'        => 'slider',
                'settings'    => 'button_border_radius_md',
                'label'       => esc_html__('Radius medium', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => 8,
                'choices'     => [
                    'min'  => 0,
                    'max'  => 12,
                    'step' => 2,
                ],
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-border-radius-md',
                    'unit'      => 'px'
                ],
            ]
        ], ['label' => esc_html__('Tailor radius', 'municipio'), 'settings' => 'button_radius']);*/ 

        /**
         * Color - Primary
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'color',
                'settings'    => 'button_color_primary',
                'label'       => esc_html__('Primary button color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => Kirki::get_option('color_palette_primary')['dark'] ?? '#eee',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-color--primary'
                ]
            ],
            [
                'type'        => 'color',
                'settings'    => 'button_color_primary_contrasting',
                'label'       => esc_html__('Primary button color - Contrast', 'municipio'),
                'description' => esc_html__('This color should be a monotone color, with a high contrast value towards the color above.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => Kirki::get_option('color_palette_primary')['contrasting'] ?? '#000',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-color-contrast--primary'
                ]
            ]
        ], ['label' => esc_html__('Tailor Color: Primary', 'municipio'), 'settings' => 'button_primary_color_active']);

        /**
         * Color - Secondary
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'color',
                'settings'    => 'button_color_secondary',
                'label'       => esc_html__('Secondary button color', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => Kirki::get_option('color_palette_secondary')['dark'] ?? '#eee',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-color--secondary'
                ]
            ],
            [
                'type'        => 'color',
                'settings'    => 'button_color_secondary_contrasting',
                'label'       => esc_html__('Secondary button color - Contrast', 'municipio'),
                'description' => esc_html__('This color should be a monotone color, with a high contrast value towards the color above.', 'municipio'),
                'section'     => self::SECTION_ID,
                'default'     => Kirki::get_option('color_palette_secondary')['contrasting'] ?? '#000',
                'output'      => [
                    'element'   => ':root',
                    'property'  => '--c-button-color-contrast--secondary'
                ]
            ]
        ], ['label' => esc_html__('Tailor Color: Secondary', 'municipio'), 'settings' => 'button_secondary_color_active']);
    }
}
