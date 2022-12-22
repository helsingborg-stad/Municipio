<?php

namespace Municipio\Customizer\Sections;

class PrimaryMenu
{
    public const SECTION_ID = "municipio_customizer_section_primary_menu";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Primary menu', 'municipio'),
            'description' => esc_html__('Primary menu settings', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'switch',
            'settings'    => 'primary_menu_dropdown',
            'label'       => esc_html__('Show subitems as dropdown', 'municipio'),
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
