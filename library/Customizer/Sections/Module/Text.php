<?php

namespace Municipio\Customizer\Sections\Module;

use Municipio\Customizer\KirkiField;

class Text
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'mod_text_modifier',
            'label'    => esc_html__('List', 'municipio'),
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
                    'context' => ['module.text.box']
                ]
            ],
        ]);
    }
}
