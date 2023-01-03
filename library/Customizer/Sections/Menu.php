<?php

namespace Municipio\Customizer\Sections;

class Menu
{
    public const SECTION_ID = "municipio_customizer_section_menu";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Menu behaviour', 'municipio'),
            'description' => esc_html__('Menu behaviour settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'primary_menu_pagetree_fallback',
            'label'       => esc_html__('Use page tree as fallback for primary menu', 'municipio'),
            'section'     => self::SECTION_ID,
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
            'section'     => self::SECTION_ID,
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
            'section'     => self::SECTION_ID,
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
            'section'     => self::SECTION_ID,
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
            'section'     => self::SECTION_ID,
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
