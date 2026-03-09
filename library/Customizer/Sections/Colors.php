<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Colors
{
    public function __construct($sectionID)
    {

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_primary',
            'label'     => esc_html__('Primary colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#ae0b05',
                'dark'        => '#770000',
                'light'       => '#e84c31',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-primary',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-primary-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-primary-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-primary-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_secondary',
            'label'     => esc_html__('Secondary colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#ec6701',
                'dark'        => '#b23700',
                'light'       => '#ff983e',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-secondary',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-secondary-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-secondary-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-secondary-contrasting',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_background',
            'label'     => esc_html__('Background colors', 'municipio'),
            'section'   => $sectionID,
            'alpha'     => true,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'background'    => esc_html__('Background', 'municipio'),
                'complementary' => esc_html__('Complementary', 'municipio'),
            ],
            'default'   => [
                'background'    => '#f5f5f5',
                'complementary' => '#0000000a',
            ],
            'output'    => [
                [
                    'choice'   => 'background',
                    'element'  => ':root',
                    'property' => '--color-background',
                ],
                [
                    'choice'   => 'complementary',
                    'element'  => ':root',
                    'property' => '--color-background-complementary',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_card',
            'label'     => esc_html__('Card & Paper colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'background' => esc_html__('Background', 'municipio'),
                'border'     => esc_html__('Border', 'municipio'),
            ],
            'default'   => [
                'background' => '#ffffff',
                'border'     => '#0000000a',
            ],
            'output'    => [
                [
                    'choice'   => 'background',
                    'element'  => ':root',
                    'property' => '--color-background-card',
                ],
                [
                    'choice'   => 'border',
                    'element'  => ':root',
                    'property' => '--color-border-card',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_text',
            'label'     => esc_html__('Text colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'      => esc_html__('Base', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
                'disabled'  => esc_html__('Disabled', 'municipio'),
            ],
            'default'   => [
                'base'      => '#000000',
                'secondary' => 'rgba(0,0,0,0.7)',
                'disabled'  => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-base',
                ],
                [
                    'choice'   => 'secondary',
                    'element'  => ':root',
                    'property' => '--text-secondary',
                ],
                [
                    'choice'   => 'disabled',
                    'element'  => ':root',
                    'property' => '--text-disabled',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_border',
            'label'     => esc_html__('Border colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'divider' => esc_html__('Divider', 'municipio'),
                'outline' => esc_html__('Outline', 'municipio'),
            ],
            'default'   => [
                'divider' => 'rgba(0,0,0,0.1)',
                'outline' => 'rgba(0,0,0,0.1)',
            ],
            'output'    => [
                [
                    'choice'   => 'divider',
                    'element'  => ':root',
                    'property' => '--color-border-divider',
                ],
                [
                    'choice'   => 'outline',
                    'element'  => ':root',
                    'property' => '--color-border-outline',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_input',
            'label'     => esc_html__('Input colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'border' => esc_html__('Border', 'municipio'),
            ],
            'default'   => [
                'border' => '#0000000a',
            ],
            'output'    => [
                [
                    'choice'   => 'border',
                    'element'  => ':root',
                    'property' => '--color-border-input',
                ]
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_link',
            'label'     => esc_html__('Link colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'link'          => esc_html__('Link', 'municipio'),
                'link_hover'    => esc_html__('Hover', 'municipio'),
                'active'        => esc_html__('Active', 'municipio'),
                'visited'       => esc_html__('Visited', 'municipio'),
                'visited_hover' => esc_html__('Visited hover', 'municipio'),
            ],
            'default'   => [
                'link'          => '#770000',
                'link_hover'    => '#ae0b05',
                'active'        => '#ae0b05',
                'visited'       => '#770000',
                'visited_hover' => '#ae0b05',
            ],
            'output'    => [
                [
                    'choice'   => 'link',
                    'element'  => ':root',
                    'property' => '--color-link',
                ],
                [
                    'choice'   => 'link_hover',
                    'element'  => ':root',
                    'property' => '--color-link-hover',
                ],
                [
                    'choice'   => 'active',
                    'element'  => ':root',
                    'property' => '--color-link-active',
                ],
                [
                    'choice'   => 'visited',
                    'element'  => ':root',
                    'property' => '--color-link-visited',
                ],
                [
                    'choice'   => 'visited_hover',
                    'element'  => ':root',
                    'property' => '--color-link-visited-hover',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_alpha',
            'label'     => esc_html__('Alpha colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'alpha'     => true,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'contrasting' => esc_html__('Base Contrasting', 'municipio'),
            ],
            'default'   => [
                'base'        => 'rgba(0,0,0,0.55)',
                'contrasting' => 'rgb(255,255,255)',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-alpha',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-alpha-contrasting',
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_success',
            'label'     => esc_html__('Success colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#91d736',
                'dark'        => '#91d736',
                'light'       => '#91d736',
                'contrasting' => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-success',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-success-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-success-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-success-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_danger',
            'label'     => esc_html__('Danger colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#d73740',
                'dark'        => '#d73740',
                'light'       => '#d73740',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-danger',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-danger-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-danger-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-danger-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_warning',
            'label'     => esc_html__('Warning colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#efbb21',
                'dark'        => '#efbb21',
                'light'       => '#efbb21',
                'contrasting' => '#000000',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-warning',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-warning-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-warning-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-warning-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_state_info',
            'label'     => esc_html__('Info colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'base'        => esc_html__('Base', 'municipio'),
                'dark'        => esc_html__('Dark', 'municipio'),
                'light'       => esc_html__('Light', 'municipio'),
                'contrasting' => esc_html__('Contrastring', 'municipio'),
            ],
            'default'   => [
                'base'        => '#3d3d3d',
                'dark'        => '#3d3d3d',
                'light'       => '#3d3d3d',
                'contrasting' => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'base',
                    'element'  => ':root',
                    'property' => '--color-info',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-info-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-info-light',
                ],
                [
                    'choice'   => 'contrasting',
                    'element'  => ':root',
                    'property' => '--color-info-contrasting',
                ],
            ]
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_complement',
            'label'     => esc_html__('Complement colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'default'  => esc_html__('Standard', 'municipio'),
                'light'    => esc_html__('Light', 'municipio'),
                'lighter'  => esc_html__('Lighter', 'municipio'),
                'lightest' => esc_html__('Lightest', 'municipio'),
            ],
            'default'   => [
                'default'  => '#dec2c2',
                'light'    => '#f0dbd9',
                'lighter'  => '#f5e4e3',
                'lightest' => '#faeeec',
            ],
            'output'    => [
                [
                    'choice'   => 'default',
                    'element'  => ':root',
                    'property' => '--color-complementary',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-complementary-light',
                ],
                [
                    'choice'   => 'lighter',
                    'element'  => ':root',
                    'property' => '--color-complementary-lighter',
                ],
                [
                    'choice'   => 'lightest',
                    'element'  => ':root',
                    'property' => '--color-complementary-lightest',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'      => 'multicolor',
            'settings'  => 'color_palette_monotone',
            'label'     => esc_html__('Monotone colors', 'municipio'),
            'section'   => $sectionID,
            'priority'  => 10,
            'transport' => 'auto',
            'alpha'     => true,
            'choices'   => [
                'black'    => esc_html__('Black', 'municipio'),
                'darkest'  => esc_html__('Darkest', 'municipio'),
                'darker'   => esc_html__('Darker', 'municipio'),
                'dark'     => esc_html__('Dark', 'municipio'),
                'light'    => esc_html__('Light', 'municipio'),
                'lighter'  => esc_html__('Lighther', 'municipio'),
                'lightest' => esc_html__('Lightest', 'municipio'),
                'white'    => esc_html__('White', 'municipio'),
            ],
            'default'   => [
                'default'  => '#f5f5f5',
                'black'    => '#000000',
                'darkest'  => '#3d3d3d',
                'darker'   => '#565656',
                'dark'     => '#707070',
                'light'    => '#a3a3a3',
                'lighter'  => '#e5e5e5',
                'lightest' => '#fcfcfc',
                'white'    => '#ffffff',
            ],
            'output'    => [
                [
                    'choice'   => 'default',
                    'element'  => ':root',
                    'property' => '--color-default',
                ],
                [
                    'choice'   => 'black',
                    'element'  => ':root',
                    'property' => '--color-black',
                ],
                [
                    'choice'   => 'darkest',
                    'element'  => ':root',
                    'property' => '--color-darkest',
                ],
                [
                    'choice'   => 'darker',
                    'element'  => ':root',
                    'property' => '--color-darker',
                ],
                [
                    'choice'   => 'dark',
                    'element'  => ':root',
                    'property' => '--color-dark',
                ],
                [
                    'choice'   => 'light',
                    'element'  => ':root',
                    'property' => '--color-light',
                ],
                [
                    'choice'   => 'lighter',
                    'element'  => ':root',
                    'property' => '--color-lighter',
                ],
                [
                    'choice'   => 'lightest',
                    'element'  => ':root',
                    'property' => '--color-lightest',
                ],
                [
                    'choice'   => 'white',
                    'element'  => ':root',
                    'property' => '--color-white',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'        => 'multicolor',
            'settings'    => 'color_palette_additional',
            'label'       => esc_html__('Additional colors', 'municipio'),
            'description' => esc_html__('These colors are used by various color pickers throughout the theme.', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport'   => 'auto',
            'alpha'       => false,
            'choices'     => [
                'additional_color_1' => esc_html__('Color #1', 'municipio'),
                'additional_color_2' => esc_html__('Color #2', 'municipio'),
                'additional_color_3' => esc_html__('Color #3', 'municipio'),
                'additional_color_4' => esc_html__('Color #4', 'municipio'),
                'additional_color_5' => esc_html__('Color #5', 'municipio'),
                'additional_color_6' => esc_html__('Color #6', 'municipio'),
            ],
            'output'      => [
                [
                    'choice'   => 'additional_color_1',
                    'element'  => ':root',
                    'property' => '--color-additional-1',
                ],
                [
                    'choice'   => 'additional_color_2',
                    'element'  => ':root',
                    'property' => '--color-additional-2',
                ],
                [
                    'choice'   => 'additional_color_3',
                    'element'  => ':root',
                    'property' => '--color-additional-3',
                ],
                [
                    'choice'   => 'additional_color_4',
                    'element'  => ':root',
                    'property' => '--color-additional-4',
                ],
                [
                    'choice'   => 'additional_color_5',
                    'element'  => ':root',
                    'property' => '--color-additional-5',
                ],
                [
                    'choice'   => 'additional_color_6',
                    'element'  => ':root',
                    'property' => '--color-additional-6',
                ],
            ],
        ]);
    }
}
