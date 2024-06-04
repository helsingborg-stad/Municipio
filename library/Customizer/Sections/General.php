<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class General
{
    public function __construct($sectionID)
    {
        KirkiField::addField([
          'type'     => 'radio',
          'settings' => 'secondary_navigation_position',
          'label'    => esc_html__('Secondary navigation position', 'municipio'),
          'section'  => $sectionID,
          'default'  => 'left',
          'priority' => 10,
          'choices'  => [
            'left'   => esc_html__('Left', 'kirki'),
            'right'  => esc_html__('Right', 'kirki'),
            'hidden' => esc_html__('Hidden', 'kirki'),
          ],
          'output'   => [
            ['type' => 'controller']
          ],
        ]);

        KirkiField::addField([
          'type'     => 'checkbox_switch',
          'settings' => 'show_emblem_in_hero',
          'label'    => esc_html__('Show emblem in hero', 'municipio'),
          'section'  => $sectionID,
          'default'  => true,
          'priority' => 10,
          'output'   => [
            ['type' => 'controller']
          ],
        ]);
    }
}
