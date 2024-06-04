<?php

namespace Municipio\Customizer\Sections\Module;

use Municipio\Customizer\KirkiField;

class Index
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'mod_index_modifier',
            'label'    => esc_html__('Index', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'none',
            'priority' => 10,
            'choices'  => [
              'none'      => esc_html__('None', 'municipio'),
              'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output'   => [
                [
                  'type'    => 'modifier',
                  'context' => ['module.index']
                ]
            ],
          ]);
    }
}
