<?php

namespace Municipio\Customizer\Sections;

use Municipio\Helper\KirkiCondidional as KirkiCondidional;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Slider
{
    public const SECTION_ID = "municipio_customizer_section_component_slider";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Slider', 'municipio'),
            'description' => esc_html__('Settings for sliders.', 'municipio'),
            'panel'       => $panelID,
            'priority'    => 160,
        ));

        /**
         * Slider gap
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'slider_gap',
            'label'       => esc_html__('Gap between slides', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 2,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'      => [
                [
                    'element'   => ':root',
                    'property'  => '--c-slider-gap',
                    'unit'      => ''
                ]
            ],
        ]);

        /**
         * Slider padding
         */
        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'slider',
            'settings'    => 'slider_padding',
            'label'       => esc_html__('Slider padding', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 7,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'      => [
                [
                    'element'   => ':root',
                    'property'  => '--c-slider-padding',
                    'unit'      => ''
                ]
            ],
        ]);
    }
}
