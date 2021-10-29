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
              'default' => ($this->base * 160),
              'minWidth' => ($this->base * 113),
              'maxWidth' => ($this->base * 205)
            ],
            [
                'key' => '_frontpage',
                'label' => esc_html__("Front Page", 'municipio'),
                'default' => ($this->base * 160),
                'minWidth' => ($this->base * 113),
                'maxWidth' => ($this->base * 205)
            ],
            [
              'key' => '_archive',
              'label' => esc_html__("Archives", 'municipio'),
              'default' => ($this->base * 160),
              'minWidth' => ($this->base * 113),
              'maxWidth' => ($this->base * 205)
            ],
            [
              'key' => '_content',
              'label' => esc_html__("Content", 'municipio'),
              'default' => ($this->base * 160),
              'minWidth' => ($this->base * 50),
              'maxWidth' => ($this->base * 113)
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
                    'min'  => $args['minWidth'],
                    'max'  => $args['maxWidth'],
                    'step' => $this->base,
                ],
                'output' => [
                    'type' => 'controller'
                ],
            ]);
        }

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'multiple'    => false,
          'settings'    => 'column_size_left',
          'label'       => esc_html__('Left', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'normal',
          'priority'    => 15,
          'choices'     => [
              'normal' => esc_html__('Normal', 'municipio'),
              'large' => esc_html__('Large', 'municipio'),
          ],
          'output' => [
              'type' => 'controller'
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'multiple'    => false,
          'settings'    => 'column_size_right',
          'label'       => esc_html__('Right', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'normal',
          'priority'    => 20,
          'choices'     => [
              'normal' => esc_html__('Normal', 'municipio'),
              'large' => esc_html__('Large', 'municipio'),
          ],
          'output' => [
              'type' => 'controller'
          ],
        ]);
    }
}
