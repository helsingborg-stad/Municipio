<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\CustomizerField;

class General
{
    public function __construct($sectionID)
    {
        CustomizerField::addField([
            'type' => 'checkbox_switch',
            'settings' => 'show_emblem_in_hero',
            'label' => esc_html__('Show emblem in hero', 'municipio'),
            'section' => $sectionID,
            'default' => true,
            'priority' => 10,
            'output' => [
                ['type' => 'controller'],
            ],
        ]);
    }
}
