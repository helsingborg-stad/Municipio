<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer as Customizer;
use Kirki as Kirki;

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
            'label'       => esc_html__('Primary colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
            'label'       => esc_html__('Secondary colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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

        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'color_background',
                'label'       => esc_html__('Background colors', 'municipio'),
                'section'     => self::SECTION_ID,
                'priority'    => 10,
                    'transport' => 'auto',
                'choices'     => [
                    'background'    => esc_html__('Background', 'municipio'),
                    'complementary'   => esc_html__('Complementary', 'municipio'),
                ],
                'default'     => [
                    'background'    => '#f5f5f5',
                    'complementary'   => '',
                ],
                'output' => [
                    [
                        'choice'    => 'background',
                        'element'   => ':root',
                        'property'  => '--color-background',
                    ],
                    [
                        'choice'    => 'complementary',
                        'element'   => ':root',
                        'property'  => '--color-background-complementary',
                    ],
                ]
            ]
        );

        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'color_card',
                'label'       => esc_html__('Card & Paper colors', 'municipio'),
                'section'     => self::SECTION_ID,
                'priority'    => 10,
                    'transport' => 'auto',
                'choices'     => [
                    'background'    => esc_html__('Background', 'municipio'),
                    'border'   => esc_html__('Border', 'municipio'),
                ],
                'default'     => [
                    'background'    => '#ffffff',
                    'border'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'background',
                        'element'   => ':root',
                        'property'  => '--color-background-card',
                    ],
                    [
                        'choice'    => 'border',
                        'element'   => ':root',
                        'property'  => '--color-border-card',
                    ],
                ]
            ]
        );

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_text',
            'label'       => esc_html__('Text colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'secondary'   => esc_html__('Secondary', 'municipio'),
                'disabled'  => esc_html__('Disabled', 'municipio'),
            ],
            'default'     => [
                'base'    => '#000000',
                'secondary'   => 'rgba(0,0,0,0.7)',
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

        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'color_border',
                'label'       => esc_html__('Border colors', 'municipio'),
                'section'     => self::SECTION_ID,
                'priority'    => 10,
                    'transport' => 'auto',
                'choices'     => [
                    'divider'    => esc_html__('Divider', 'municipio'),
                    'outline'    => esc_html__('Outline', 'municipio'),
                ],
                'default'     => [
                    'divider'    => 'rgba(0,0,0,0.1)',
                    'outline'    => 'rgba(0,0,0,0.1)',
                ],
                'output' => [
                    [
                        'choice'    => 'divider',
                        'element'   => ':root',
                        'property'  => '--color-border-divider',
                    ],
                    [
                        'choice'    => 'outline',
                        'element'   => ':root',
                        'property'  => '--color-border-outline',
                    ],
                ]
            ]
        );

        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'color_input',
                'label'       => esc_html__('Input colors', 'municipio'),
                'section'     => self::SECTION_ID,
                'priority'    => 10,
                    'transport' => 'auto',
                'choices'     => [
                    'border'   => esc_html__('Border', 'municipio'),
                ],
                'default'     => [
                    'border'    => '',
                ],
                'output' => [
                    [
                        'choice'    => 'border',
                        'element'   => ':root',
                        'property'  => '--color-border-input',
                    ]
                ]
            ]
        );

        \Kirki::add_field(
            \Municipio\Customizer::KIRKI_CONFIG,
            [
                'type'        => 'multicolor',
                'settings'    => 'color_link',
                'label'       => esc_html__('Link colors', 'municipio'),
                'section'     => self::SECTION_ID,
                'priority'    => 10,
                'transport' => 'auto',
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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_alpha',
            'label'       => esc_html__('Alpha colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'alpha'       => true,
            'transport'   => 'auto',
            'choices'     => [
                'base'          => esc_html__('Base', 'municipio'),
                'contrasting'   => esc_html__('Base Contrasting', 'municipio'),
            ],
            'default'     => [
                'base'          => 'rgba(0,0,0,0.55)',
                'contrasting'   => 'rgb(255,255,255)',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-alpha',
                ],
                [
                    'choice'    => 'contrasting',
                    'element'   => ':root',
                    'property'  => '--color-alpha-contrasting',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_success',
            'label'       => esc_html__('Success colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_danger',
            'label'       => esc_html__('Danger colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_warning',
            'label'       => esc_html__('Warning colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_state_info',
            'label'       => esc_html__('Info colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_complement',
            'label'       => esc_html__('Complement colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'default'   => esc_html__('Standard', 'municipio'),
                'light'  => esc_html__('Light', 'municipio'),
                'lighter'  => esc_html__('Lighter', 'municipio'),
                'lightest'  => esc_html__('Lightest', 'municipio'),
            ],
            'default'     => [
                'default'   => '#dec2c2',
                'light'  => '#f0dbd9',
                'lighter'  => '#f5e4e3',
                'lightest'  => '#faeeec',
            ],
            'output' => [
                [
                    'choice'    => 'default',
                    'element'   => ':root',
                    'property'  => '--color-complementary',
                ],
                [
                    'choice'    => 'light',
                    'element'   => ':root',
                    'property'  => '--color-complementary-light',
                ],
                [
                    'choice'    => 'lighter',
                    'element'   => ':root',
                    'property'  => '--color-complementary-lighter',
                ],
                [
                    'choice'    => 'lightest',
                    'element'   => ':root',
                    'property'  => '--color-complementary-lightest',
                ],
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_monotone',
            'label'       => esc_html__('Monotone colors', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'transport' => 'auto',
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
    }
}
