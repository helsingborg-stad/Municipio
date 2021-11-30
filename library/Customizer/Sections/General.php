<?php

namespace Municipio\Customizer\Sections;

class General
{
    public const SECTION_ID = "municipio_customizer_section_general";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('General', 'municipio'),
            'description' => esc_html__('General settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'secondary_navigation_position',
          'label'       => esc_html__('Secondary navigation position', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'left',
          'priority'    => 10,
          'choices'     => [
            'left'   => esc_html__('Left', 'kirki'),
            'right' => esc_html__('Right', 'kirki'),
            'hidden'  => esc_html__('Hidden', 'kirki'),
          ],
          'output' => [
            'type' => 'controller'
          ],
        ]);
    }
}
