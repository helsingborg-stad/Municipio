<?php

namespace Municipio\Customizer\Sections;

class Overlay
{
    public const SECTION_ID = "municipio_customizer_section_overlay";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Overlay', 'municipio'),
            'description' => esc_html__('Set a overlay, the color will be overlayed on images to enhance readability.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));
 
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'color_general_overlay',
            'label'       => __('General overlay color','kirki'),
            'description' => esc_html__( 'Set a general overlay color.', 'kirki' ),
            'section'     => self::SECTION_ID,
            'default'     => 'rgba(0, 0, 0, .6);',
            'choices'     => [
                'alpha' => true,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--color-general-overlay'
            ]
        ]);
    }
}
