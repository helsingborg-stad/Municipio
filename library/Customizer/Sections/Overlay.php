<?php

namespace Municipio\Customizer\Sections;

class Overlay
{
    public const SECTION_ID = "municipio_customizer_section_overlay";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Overlay', 'municipio'),
            'description' => esc_html__('Overlay settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'overlay',
          'label'       => esc_html__('Default overlay', 'municipio'),
          'description' => esc_html__("Choose a default overlaycolor to use when there's nothing defined.", 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'rgba(0,0,0,.6)',
          'output'      => [
            'element'   => ':root',
            'property'  => '--color-general-overlay'
          ],
          'choices'     => [
            'alpha' => true,
          ]
        ]);
    }
}
