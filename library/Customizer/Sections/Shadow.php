<?php

namespace Municipio\Customizer\Sections;

class Shadow
{
    public function __construct(string $sectionID)
    {
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'drop_shadow_amount',
            'label'       => esc_html__('Amount of shadows', 'municipio'),
            'description' => esc_html__('The shadow sizes will automatically be multiplied from the value below. A value of 0 will completly turn off shadows.', 'municipio'),
            'section'     => $sectionID,
            'transport' => 'auto',
            'default'     => 1,
            'choices'     => [
                'min'  => 0,
                'max'  => 4,
                'step' => 0.1,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--drop-shadow-amount',
                ]
            ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'color',
            'settings'    => 'drop_shadow_color',
            'label'       => esc_html__('Color of shadows', 'municipio'),
            'description' => esc_html__('What color to use for shadows.', 'municipio'),
            'section'     => $sectionID,
            'transport'   => 'auto',
            'default'     => 'rgba(0,0,0,0.3)',
            'choices'     => [
                'alpha' => true,
            ],
            'output' => [
                [
                    'element'   => ':root',
                    'property'  => '--drop-shadow-color',
                ]
            ],
        ]);
    }
}
