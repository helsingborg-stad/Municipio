<?php

namespace Municipio\Customizer\Sections;

class Shadow
{
    public const SECTION_ID = "municipio_customizer_section_shadow";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Drop Shadows', 'municipio'),
            'description' => esc_html__('Adjust general drop shadows. ', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'shadow_amount',
            'label'       => esc_html__('Amount of shadows', 'municipio'),
            'description' => esc_html__('The shadow sizes will automatically be multiplied from the value below. A value of 0 will completly turn off shadows.', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 2,
                'step' => 1,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--shadow-amount',
            ],
        ]);
    }
}
