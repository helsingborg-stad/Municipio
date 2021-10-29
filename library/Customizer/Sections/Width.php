<?php

namespace Municipio\Customizer\Sections;

class Width
{
    public const SECTION_ID = "municipio_customizer_section_width";

    private $variations = []; 
    private $base = 8; 

    public function __construct($panelID)
    {
        $this->variations = [
            [
              'key' => '',
              'label' => esc_html__("Default", 'municipio'),
              'default' => ($this->base * 160)
            ],
            [
                'key' => '_frontpage',
                'label' => esc_html__("Front Page", 'municipio'),
                'default' => ($this->base * 160)
            ],
            [
              'key' => '_archive',
              'label' => esc_html__("Archives", 'municipio'),
              'default' => ($this->base * 160)
            ],
            [
              'key' => '_content',
              'label' => esc_html__("Content", 'municipio'),
              'default' => ($this->base * 160)
            ]
        ]; 

        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Page Widths', 'municipio'),
            'description' => esc_html__('Set the maximum page withs of different page types.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        foreach ($this->variations as $key => $args) {
            \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
                'type'        => 'slider',
                'settings'    => 'municipio_container' . $args['key'],
                'label'       => $args['label'],
                'section'     => self::SECTION_ID,
                'default'     => $args['default'],
                'choices'     => [
                    'min'  => ($this->base * 113),
                    'max'  => ($this->base * 205),
                    'step' => $this->base,
                ],
                'output' => [
                    'type' => 'controller'
                ],
            ]);
        }
    }
}
