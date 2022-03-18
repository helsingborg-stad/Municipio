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
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output' => [
                ['type' => 'controller']
            ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'hamburger_menu_parent_buttons',
            'label'       => esc_html__('Show parents as buttons', 'municipio'),
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
    }
}
