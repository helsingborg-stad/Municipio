<?php

namespace Municipio\Customizer\Sections;

class Field
{
    public const SECTION_ID = "municipio_customizer_section_field";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Field', 'municipio'),
            'description' => esc_html__('Field settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'field_style_settings',
            'label'       => esc_html__('Field style', 'municipio'),
            'description' => esc_html__('Which styling the input field use.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''   => esc_html__('Default', 'municipio'),
                'rounded' => esc_html__('Rounded', 'municipio')
            ],
            'output' => [
                [
                  'type' => 'modifier',
                  'context' => [
                    'component.field',
                    'component.select',
                    'component.form'
                  ]
                ]
            ],
        ]);
    }
}
