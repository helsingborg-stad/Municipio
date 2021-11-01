<?php

namespace Municipio\Customizer\Sections;

class Radius
{
    public const SECTION_ID = "municipio_customizer_section_radius";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Rounded corners', 'municipio'),
            'description' => esc_html__('Adjust the roundness of corners on the site overall. The sizes are applied where they are suitable, additional component adjustments can be made under the components tab.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'municipio_radius_xs',
            'label'       => esc_html__('Extra small', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 2,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--radius-xs',
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'municipio_radius_sm',
            'label'       => esc_html__('Small', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 4,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--radius-sm',
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'municipio_radius_md',
            'label'       => esc_html__('Medium', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 6,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--radius-md',
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'municipio_radius_lg',
            'label'       => esc_html__('Large', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 8,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 2,
            ],
            'output' => [
                'element'   => ':root',
                'property'  => '--radius-lg',
            ],
        ]);
        
    }
}
