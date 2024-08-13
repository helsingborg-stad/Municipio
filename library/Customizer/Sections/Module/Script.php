<?php

namespace Municipio\Customizer\Sections\Module;

use Municipio\Customizer\KirkiField;

class Script
{
    public const SECTION_ID = "municipio_customizer_section_mod_script";

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'mod_script_modifier',
            'label'    => esc_html__('Script', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'none',
            'priority' => 10,
            'choices'  => [
                'none'      => esc_html__('None', 'municipio'),
                'panel'     => esc_html__('Panel', 'municipio'),
                'accented'  => esc_html__('Accented', 'municipio'),
                'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output'   => [
                [
                    'type'    => 'modifier',
                    'context' => ['module.script']
                ]
            ],
        ]);
    }
}
