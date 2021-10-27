<?php

namespace Municipio\Customizer\Sections;

class Palette
{
    public const SECTION_ID = "municipio_customizer_section_palette";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Color Palette', 'municipio'),
            'description' => esc_html__('Color palette used across the theme', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_primary',
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
                'base'    => '#ae0b05',
                'dark'   => '#770000',
                'light'  => '#e84c31',
                'contrasting'  => '#ffffff',
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_secondary',
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
                'base'    => '#ec6701',
                'dark'   => '#b23700',
                'light'  => '#ff983e',
                'contrasting'  => '#ffffff',
            ],
            'output' => [
                [
                    'element' => '.lol',
                    'property' => 'background-color',
                ]
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_tertiary',
            'label'       => esc_html__('Tertiary', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#dec2c2',
                'dark'   => '#f0dbd9',
                'light'  => '#f5e4e3',
                'contrasting'  => '#ffffff',
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_success',
            'label'       => esc_html__('Success', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#91d736',
                'dark'   => '#91d736',
                'light'  => '#91d736',
                'contrasting'  => '#ffffff',
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_danger',
            'label'       => esc_html__('Danger', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#d73740',
                'dark'   => '#d73740',
                'light'  => '#d73740',
                'contrasting'  => '#ffffff',
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_warning',
            'label'       => esc_html__('Warning', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#efbb21',
                'dark'   => '#efbb21',
                'light'  => '#efbb21',
                'contrasting'  => '#ffffff',
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_info',
            'label'       => esc_html__('Info', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'contrasting'  => esc_html__('Contrastring', 'municipio'),
            ],
            'default'     => [
                'base'    => '#3d3d3d',
                'dark'   => '#3d3d3d',
                'light'  => '#3d3d3d',
                'contrasting'  => '#ffffff',
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_background',
            'label'       => esc_html__('Background', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'background'    => esc_html__('Background', 'municipio'),
                'card'   => esc_html__('Card', 'municipio'),
            ],
            'default'     => [
                'background'    => '#f5f5f5',
                'card'   => '#ffffff',
            ],
        ]);
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_text',
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
                'disabled'  => '#ffffff',
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_link',
            'label'       => esc_html__('Link', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'link'    => esc_html__('Link', 'municipio'),
                'link_hover'   => esc_html__('Hover', 'municipio'),
                'active'  => esc_html__('Active', 'municipio'),
                'visited'  => esc_html__('Visited', 'municipio'),
            ],
            'default'     => [
                'link'    => '#0088cc',
                'link_hover'   => '#00aaff',
                'active'  => '#ffffff',
                'visited'  => '#ffffff',
                'visited_hover'  => '#ffffff',
            ],
        ]);
    }
}
