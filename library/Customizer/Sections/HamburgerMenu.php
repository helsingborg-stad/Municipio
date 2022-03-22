<?php

namespace Municipio\Customizer\Sections;

class HamburgerMenu
{
    public const SECTION_ID = "municipio_customizer_section_hamburger_menu";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Hamburger menu', 'municipio'),
            'description' => esc_html__('Hamburger menu settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'hamburger_menu_mobile',
            'label'       => esc_html__('Show on mobile', 'municipio'),
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

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hamburger_menu_trigger_style',
            'label'       => esc_html__('Select menu trigger style', 'municipio'),
            'section'     => self::SECTION_ID,
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
            'type'        => 'select',
            'settings'    => 'hamburger_menu_parent_style',
            'label'       => esc_html__('Select menu parent style', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'text',
            'priority'    => 10,
            'choices' => [
                false  => esc_html__('None', 'municipio'),
                'basic' => esc_html__('Basic button', 'municipio'),
                'filled' => esc_html__('Filled button', 'municipio'),
                'outlined' => esc_html__('Outlined button', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);
    }
}
