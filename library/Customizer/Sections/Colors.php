<?php

namespace Municipio\Customizer\Sections;

class Colors
{
    public const SECTION_ID = "municipio_customizer_section_colors";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Colors', 'municipio'),
            'description' => esc_html__('Colors used across the theme.', 'municipio'),
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
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-primary',
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
            ]
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
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-secondary',
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
            'settings'    => 'color_palette_tertiary',
            'label'       => esc_html__('Monotone', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'black'   => esc_html__('Black', 'municipio'),
                'darkest'  => esc_html__('Darkest', 'municipio'),
                'darker'  => esc_html__('Darker', 'municipio'),
                'dark'  => esc_html__('Dark', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'lighter'  => esc_html__('Lighther', 'municipio'),
                'lightest'  => esc_html__('Lightest', 'municipio'),
                'white'    => esc_html__('White', 'municipio'),
            ],
            'default'     => [
                'default'   => '#f5f5f5',
                'black'   => '#000000',
                'darkest'  => '#3d3d3d',
                'darker'  => '#565656',
                'dark'  => '#707070',
                'light'  => '#a3a3a3',
                'lighter'  => '#e5e5e5',
                'lightest'  => '#fcfcfc',
                'white'    => '#ffffff',
            ],
            'output' => [
                [
                    'choice'    => 'default',
                    'element'   => ':root',
                    'property'  => '--color-default',
                ],
                [
                    'choice'    => 'black',
                    'element'   => ':root',
                    'property'  => '--color-black',
                ],
                [
                    'choice'    => 'darkest',
                    'element'   => ':root',
                    'property'  => '--color-darkest',
                ],
                [
                    'choice'    => 'darker',
                    'element'   => ':root',
                    'property'  => '--color-darker',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-light',
                ],
                [
                    'choice'    => 'lighter',
                    'element'   => ':root',
                    'property'  => '--color-lighter',
                ],
                [
                    'choice'    => 'lightest',
                    'element'   => ':root',
                    'property'  => '--color-lightest',
                ],
                [
                    'choice'    => 'white',
                    'element'   => ':root',
                    'property'  => '--color-white',
                ],
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_success',
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
                'contrasting'  => '#000000',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-success',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-success-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-success-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-success-contrasting',
                ],
        ]]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_danger',
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
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-danger',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-danger-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-danger-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-danger-contrasting',
                ],
        ]]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_warning',
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
                'contrasting'  => '#000000',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-warning',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-warning-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-warning-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-warning-contrasting',
                ],
        ]]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_info',
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
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-info',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-info-dark',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-info-light',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-info-contrasting',
                ],
        ]]);


        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_background',
            'label'       => esc_html__('Background', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'background'    => esc_html__('Background', 'municipio'),
                'paper'   => esc_html__('Paper', 'municipio'),
            ],
            'default'     => [
                'background'    => '#f5f5f5',
                'paper'   => '#ffffff',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => 'body',
                    'property'  => 'background-color',
                ],
                [
                    'choice'    => 'card',
                    'element'   => ':root',
                    'property'  => '--color-paper-background',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_text',
            'label'       => esc_html__('Text', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'secondary'   => esc_html__('Secondary', 'municipio'),
                'disabled'  => esc_html__('Disabled', 'municipio'),
            ],
            'default'     => [
                'base'    => '#000000',
                'secondary'   => '#000000',
                'disabled'  => '#000000',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-base',
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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_link',
            'label'       => esc_html__('Link', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'link'    => esc_html__('Link', 'municipio'),
                'link_hover'   => esc_html__('Hover', 'municipio'),
                'active'  => esc_html__('Active', 'municipio'),
                'visited'  => esc_html__('Visited', 'municipio'),
                'visited_hover'  => esc_html__('Visited hover', 'municipio'),
            ],
            'default'     => [
                'link'    => '#770000',
                'link_hover'   => '#ae0b05',
                'active'  => '#ae0b05',
                'visited'  => '#770000',
                'visited_hover'  => '#ae0b05',
            ],
            'output' => [
                [
                    'choice'    => 'link',
                    'element'   => ':root',
                    'property'  => '--color-link',
                ],
                [
                    'choice'    => 'link_hover',
                    'element'   => ':root',
                    'property'  => '--color-link-hover',
                ],
                [
                    'choice'    => 'active',
                    'element'   => ':root',
                    'property'  => '--color-link-active',
                ],
                [
                    'choice'    => 'visited',
                    'element'   => ':root',
                    'property'  => '--color-link-visited',
                ],
                [
                    'choice'    => 'visited_hover',
                    'element'   => ':root',
                    'property'  => '--color-link-visited-hover',
                ],
            ],
        ]
        );
    }
}
