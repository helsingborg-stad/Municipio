<?php

namespace Municipio\Customizer\Sections\Module;

class Inlay
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'mod_inlay_list_modifier',
            'label'       => esc_html__('Inlay list', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'none',
            'priority'    => 10,
            'choices'     => [
              'none' => esc_html__('None', 'municipio'),
              'panel' => esc_html__('Panel', 'municipio'),
              'accented' => esc_html__('Accented', 'municipio'),
              'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['module.inlay.list']
                ]
            ],
        ]);

    }
}
