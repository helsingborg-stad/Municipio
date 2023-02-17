<?php

namespace Municipio\Customizer\Sections\Module;

class LocalEvent
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'mod_localevent_modifier',
            'label'       => esc_html__('Local Event', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'none',
            'priority'    => 10,
            'choices'     => [
                'none' => esc_html__('None', 'municipio'),
                'panel' => esc_html__('Panel', 'municipio'),
                'accented' => esc_html__('Accented', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['module.localevent.list']
                ]
            ],
        ]);

    }
}
