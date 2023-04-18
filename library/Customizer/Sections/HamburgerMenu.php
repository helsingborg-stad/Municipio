<?php

namespace Municipio\Customizer\Sections;

class HamburgerMenu
{
    public const SECTION_ID = "municipio_customizer_section_hamburger_menu";

    public function __construct(string $sectionID)
    {
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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_trigger_style',
            'label'       => esc_html__('Select menu trigger style', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'basic',
            'priority'    => 10,
            'choices' => [
                'basic' => esc_html__('Basic button', 'municipio'),
                'filled' => esc_html__('Filled button', 'municipio'),
                'outlined' => esc_html__('Outlined button', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_hambuger_menu_heading',
            'label'       => esc_html__('Heading', 'municipio'),
            'description' => esc_html__('Select a color for the headings', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport'   => 'auto',
            'choices'     => [
                'heading'    => esc_html__('Heading', 'municipio'),
            ],
            'default'     => [
                'heading'    => '',
            ],
            'output' => [
                [
                    'choice'    => 'heading',
                    'element'   => ':root',
                    'property'  => '--color-hamburger-menu-heading',
                ],
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_hambuger_menu_subitem',
            'label'       => esc_html__('Subitem', 'municipio'),
            'description' => esc_html__('Select a color for the subitems', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'subitem'   => esc_html__('Subitem', 'municipio'),
            ],
            'default'     => [
                'subitem'   => '',
            ],
            'output' => [
                [
                    'choice'    => 'subitem',
                    'element'   => ':root',
                    'property'  => '--color-hamburger-menu-subitem',
                ],
            ]
        ]);

          \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'color_palette_hambuger_menu_background',
            'label'       => esc_html__('Background', 'municipio'),
            'description' => esc_html__('Select a color for the background', 'municipio'),
            'section'     => $sectionID,
            'priority'    => 10,
            'transport' => 'auto',
            'choices'     => [
                'background'   => esc_html__('Background', 'municipio'),
            ],
            'default'     => [
                'background'   => '',
            ],
            'output' => [
                [
                    'choice'    => 'background',
                    'element'   => ':root',
                    'property'  => '--color-hamburger-menu-background',
                ],
            ]
        ]);
    }
}
