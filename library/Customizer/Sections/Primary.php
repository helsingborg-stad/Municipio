<?php

namespace Municipio\Customizer\Sections;

class Primary
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'primary_background_type',
          'label'       => esc_html__('Select background type', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined colors, or select one freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__( 'Predefined colors', 'municipio'),
            'hex' => esc_html__('Custom color', 'municipio'),
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance',
              'operator' => '===',
              'value'    => '',
            ]
          ],
        ]);
    }
}
