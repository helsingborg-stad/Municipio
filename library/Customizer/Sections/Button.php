<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Button
{
    public function __construct(string $sectionID)
    {
        /**
         * Radius
         */
        /*KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            [
                'type'        => 'slider',
                'settings'    => 'button_border_radius_sm',
                'label'       => esc_html__('Radius small', 'municipio'),
                'section'     => $sectionID,
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
                'section'     => $sectionID,
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
            'type'        => 'multicolor',
            'settings'    => 'color_button_primary',
            'label'       => esc_html__('Primary button colors', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'base'             => esc_html__('Primary', 'municipio'),
                'contrasting' => esc_html__('Primary Contrasting', 'municipio')
            ],
            'default'     => [
                'base'  => Kirki::get_option('color_palette_primary')['base'] ?? '#eee',
                'contrasting'  => Kirki::get_option('color_palette_primary')['contrasting'] ?? '#000'
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--c-button-primary-color',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--c-button-primary-color-contrasting',
                ]
            ],
        ], ['label' => esc_html__('Tailor Color: Primary', 'municipio'), 'settings' => 'button_primary_color_active']);

        /**
         * Color - Secondary
         */
        KirkiCondidional::add_field(Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_button_secondary',
            'label'       => esc_html__('Secondary button colors', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'base'             => esc_html__('Secondary ', 'municipio'),
                'contrasting' => esc_html__('Secondary Contrasting', 'municipio')
            ],
            'default'     => [
                'base'  => Kirki::get_option('color_palette_secondary')['base'] ?? '#eee',
                'contrasting'  => Kirki::get_option('color_palette_secondary')['contrasting'] ?? '#000'
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--c-button-secondary-color',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--c-button-secondary-color-contrasting',
                ]
            ],
        ], ['label' => esc_html__('Tailor Color: Secondary', 'municipio'), 'settings' => 'button_secondary_color_active']);
    }
}
