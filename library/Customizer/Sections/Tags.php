<?php

namespace Municipio\Customizer\Sections;

class Tags
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'tags_style_settings',
            'label'       => esc_html__('Tags styling', 'municipio'),
            'description' => esc_html__('Which styling tags use.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''   => esc_html__('Default', 'municipio'),
                'pill' => esc_html__('Pill', 'municipio')
            ],
            'output' => [
                [
                  'type' => 'controller',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'tags_markings_style',
            'label'       => esc_html__('Taxonomy marking', 'municipio'),
            'description' => esc_html__('Styling the taxonomy markings.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''   => esc_html__("Default", 'municipio'),
                'taxonomy-colors' => esc_html__('Taxonomy colors', 'municipio')
            ],
        ]);
    }
}
