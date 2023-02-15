<?php

namespace Municipio\Customizer\Sections;

class Menu
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'primary_menu_pagetree_fallback',
            'label'       => esc_html__('Use page tree as fallback for primary menu', 'municipio'),
            'section'     => $sectionID,
            'default'     => true,
            'priority'    => 10,
            'choices' => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'secondary_menu_pagetree_fallback',
            'label'       => esc_html__('Use page tree as fallback for secondary menu', 'municipio'),
            'section'     => $sectionID,
            'default'     => true,
            'priority'    => 10,
            'choices' => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'mobile_menu_pagetree_fallback',
            'label'       => esc_html__('Use page tree as fallback for mobile menu', 'municipio'),
            'section'     => $sectionID,
            'default'     => true,
            'priority'    => 10,
            'choices' => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'hamburger_menu_pagetree_fallback',
            'label'       => esc_html__('Use page tree as fallback for hamburger menu', 'municipio'),
            'section'     => $sectionID,
            'default'     => false,
            'priority'    => 10,
            'choices' => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'primary_menu_dropdown',
            'label'       => esc_html__('Show subitems as dropdown in main menu', 'municipio'),
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
