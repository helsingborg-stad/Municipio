<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiSwatches as KirkiSwatches;

class HamburgerMenu
{
    public const SECTION_ID = "municipio_customizer_section_hamburger_menu";

    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'radio',
            'settings'    => 'hamburger_menu_appearance_type',
            'label'       => esc_html__('Appearance', 'municipio'),
            'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'default',
            'priority'    => 5,
            'choices'     => [
                'default' => esc_html__('Predefined appearance', 'municipio'),
                'custom' => esc_html__('Custom appearance', 'municipio'),
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'hamburger_menu_custom_colors',
            'label'       => esc_html__('Custom colors', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'heading'    => esc_html__('Heading', 'municipio'),
                'subitem'    => esc_html__('Subitem', 'municipio'),
                'background'    => esc_html__('Background', 'municipio'),
            ],
            'default' => [
                'heading'   => '#000',
                'subitem'   => '#000',
                'background' => '#fff',
            ],
            'palettes' => KirkiSwatches::getColors(),
            'output' => [
                [
                    'choice'    => 'heading',
                    'element'   => ':root',
                    'property'  => '--c-hamburger-menu-heading-color'
                ],
                [
                    'choice'    => 'subitem',
                    'element'   => ':root',
                    'property'  => '--c-hamburger-menu-subitem-color'
                ],
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--c-hamburger-menu-background-color'
                ],
            ],
            'active_callback'  => [
                    [
                    'setting'  => 'hamburger_menu_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                    ]
                ],
        ]); 

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_font',
            'label'       => esc_html__('Select font', 'municipio'),
            'description' => esc_html__('Sets the font for the main items.'),
            'section'     => $sectionID,
            'default'     => '',
            'choices'     => [
                '' => esc_html__('Body', 'municipio'),
                'font-heading' => esc_html__('Heading', 'municipio'),
            ],
            'output' => [
            [
                'type' => 'modifier',
                'context' => ['site.hamburgermenu.nav']
            ]
            ],
            'active_callback'  => [
            [
                'setting'  => 'hamburger_menu_appearance_type',
                'operator' => '===',
                'value'    => 'custom',
            ]
            ],
        ]); 

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_item_style',
            'label'       => esc_html__('Sets the style of the main items', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'default',
            'choices'     => [
                'default' => esc_html__('Default', 'municipio'),
                'button' => esc_html__('Button', 'municipio'),
            ],
            'active_callback'  => [
                [
                    'setting'  => 'hamburger_menu_appearance_type',
                    'operator' => '===',
                    'value'    => 'custom',
                ]
            ],
        ]); 

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_item_button_style',
            'label'       => esc_html__('Sets the style of the main items', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'filled',
            'choices'     => [
                'filled' => esc_html__('Filled button', 'municipio'),
                'basic' => esc_html__('Default button', 'municipio'),
                'outlined' => esc_html__('Outlined button', 'municipio'),

            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'parentStyle',
                    'context' => [
                        [
                            'context' => 'component.hamburgermenu',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
            'active_callback'  => [
            [
                'setting'  => 'hamburger_menu_appearance_type',
                'operator' => '===',
                'value'    => 'custom',
            ],
            [
                'setting'  => 'hamburger_menu_item_style',
                'operator' => '===',
                'value'    => 'button',
            ],
            ],
        ]); 

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_item_button_color',
            'label'       => esc_html__('Color of the button', 'municipio'),
            'description' => esc_html__('Sets the color of the button. The custom color will be ignored.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'primary',
            'choices'     => [
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
                'default' => esc_html__('Default', 'municipio'),

            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'parentStyleColor',
                    'context' => [
                        [
                            'context' => 'component.hamburgermenu',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
            'active_callback'  => [
            [
                'setting'  => 'hamburger_menu_appearance_type',
                'operator' => '===',
                'value'    => 'custom',
            ],
            [
                'setting'  => 'hamburger_menu_item_style',
                'operator' => '===',
                'value'    => 'button',
            ],
            ],
        ]); 
        
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_color_scheme',
            'label'       => esc_html__('Color scheme', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'primary',
            'priority'    => 10,
            'choices'     => [
                'primary' => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.hamburgermenu.nav']
                ]
            ],
            'active_callback'  => [
                [
                    'setting'  => 'hamburger_menu_appearance_type',
                    'operator' => '===',
                    'value'    => 'default',
                ]
            ],
        ]);


        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_cover_page',
            'label'       => esc_html__('Cover full page', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices' => [
                '' => esc_html__('No cover', 'municipio'),
                'cover'  => esc_html__('Cover', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['site.hamburgermenu.nav']
                ]
            ],
        ]);
        
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'hamburger_menu_mobile',
            'label'       => esc_html__('Show on mobile', 'municipio'),
            'section'     => $sectionID,
            'default'     => false,
            'priority'    => 10,
            'choices' => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);
    }
}
