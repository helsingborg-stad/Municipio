<?php

namespace Municipio\Customizer\Sections;

class Example
{
    public const SECTION_ID = "municipio_customizer_section_example";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Example', 'municipio'),
            'description' => esc_html__('A simple example panel demonstrating different setups.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'multicolor',
            'settings'    => 'example_css_var',
            'label'       => esc_html__('Example css variable setting', 'municipio'),
            'section'     => self::SECTION_ID,
            'priority'    => 10,
            'choices'     => [
                'base'    => esc_html__('Base', 'municipio'),
                'dark'   => esc_html__('Dark', 'municipio')
            ],
            'default'     => [
                'base'    => '#0088cc',
                'dark'   => '#00aaff',
            ],
            'output' => [
                [
                    'choice'    => 'base',
                    'element'   => ':root',
                    'property'  => '--color-primary-base',
                ],
                [
                    'choice'    => 'dark',
                    'element'   => ':root',
                    'property'  => '--color-primary-dark',
                ]
            ],
        ]);

        //Example modifier toggle
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'example_modifier',
            'label'       => esc_html__('Example modifier toggle', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => ['option-1'],
            'priority'    => 10,
            'choices'     => [
                'option-1' => esc_html__('Option 1', 'municipio'),
                'option-2' => esc_html__('Option 2', 'municipio'),
                'option-3' => esc_html__('Option 3', 'municipio'),
                'option-4' => esc_html__('Option 4', 'municipio'),
            ],
            'output' => [
                'type' => 'modifier',
                'context' => ['site.header', 'site.footer'],
            ],
        ]);

        //Example controller variable
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'multiple'    => false,
            'settings'    => 'example_controller_var',
            'label'       => esc_html__('Example controller variable', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'option-2',
            'priority'    => 10,
            'choices'     => [
                'option-1' => esc_html__('Option 1', 'municipio'),
                'option-2' => esc_html__('Option 2', 'municipio'),
                'option-3' => esc_html__('Option 3', 'municipio'),
                'option-4' => esc_html__('Option 4', 'municipio'),
            ],
            'output' => [
                'type' => 'controller'
            ],
        ]);
    }
}
