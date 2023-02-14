<?php

namespace Municipio\Customizer\Sections\Module;

class SectionsSplit
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'mod_section_split_modifier',
            'label'       => esc_html__('Section Split', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'none',
            'priority'    => 10,
            'choices'     => [
                'none' => esc_html__('None', 'municipio'),
                'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['sectionsSplit', 'module.sections.split']
                ]
            ],
        ]);

    }
}
