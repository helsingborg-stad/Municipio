<?php

namespace Municipio\Customizer\Sections;

class Palette
{
    public const SECTION_ID = "municipio_customizer_section_palette";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Palette', 'municipio'),
            'description' => esc_html__('Theme Color Palette', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'theme_palette_primary',
            'label'       => esc_html__('Primary', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#0088cc',
                'dark'   => '#00aaff',
                'light'  => '#00ffff',
                'contrasting'  => '#00ffff',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-primary-base',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-primary-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-primary-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-primary-contrasting',
                ],
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'theme_palette_secondary',
            'label'       => esc_html__('Secondary', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#0088cc',
                'dark'   => '#00aaff',
                'light'     => '#00ffff',
                'contrasting'  => '#00ffff',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-secondary-base',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-secondary-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-secondary-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-secondary-contrasting',
                ],
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'theme_palette_background',
            'label'       => esc_html__('Background', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'background'    => esc_html__('Background', 'municipio'),
                'card'   => esc_html__('Card', 'municipio'),
            ],
            'default'     => [
                'background'    => '#0088cc',
                'card'   => '#00aaff',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--background-card',
                ],
                [
                    'choice'    => 'card',
                    'element'   => ':root',
                    'property'  => '--background-card',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'theme_palette_text',
            'label'       => esc_html__('Text', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'secondary'   => esc_html__('Secondary', 'municipio'),
                'disabled'  => esc_html__('Disabled', 'municipio'),
            ],
            'default'     => [
                'base'    => '#0088cc',
                'secondary'   => '#00aaff',
                'disabled'  => '#00ffff',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--text-base',
                ],
                [
                    'choice'    => 'secondary',
                    'element'   => ':root',
                    'property'  => '--text-secondary',
                ],
                [
                    'choice'    => 'disabled',
                    'element'   => ':root',
                    'property'  => '--text-disabled',
                ]
            ],
        ]);
    }
}
