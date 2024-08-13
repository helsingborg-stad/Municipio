<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Width
{
    private $variations = [];
    private $base       = 8;

    public function __construct($sectionID)
    {
        $this->variations = [
            [
              'key'      => '',
              'css'      => '--container-width',
              'label'    => esc_html__("Container", 'municipio'),
              'default'  => ($this->base * 160),
              'minWidth' => ($this->base * 113),
              'maxWidth' => ($this->base * 205)
            ],
            [
              'key'      => '_content',
              'css'      => '--container-width-content',
              'label'    => esc_html__("Content", 'municipio'),
              'default'  => ($this->base * 88),
              'minWidth' => ($this->base * 50),
              'maxWidth' => ($this->base * 113)
            ]
        ];

        foreach ($this->variations as $key => $args) {
            KirkiField::addField([
                'type'     => 'slider',
                'settings' => 'container' . $args['key'],
                'label'    => $args['label'],
                'section'  => $sectionID,
                'default'  => $args['default'],
                'choices'  => [
                    'min'  => $args['minWidth'],
                    'max'  => $args['maxWidth'],
                    'step' => $this->base,
                ],
                'output'   => [
                    [
                        'element'  => ':root',
                        'property' => $args['css'],
                        'units'    => 'px',
                    ]
                ],
            ]);
        }

        KirkiField::addField([
            'type'     => 'custom',
            'settings' => 'heading_width',
            'section'  => $sectionID,
            'default'  => '
                                <h2>' . esc_html__('Width of page columns', 'municipio') . ' </h2> 
                                <p class="description customize-section-description">' . esc_html__('Set the width of left & right columns. The middle (content) column will use whatever space left.', 'municipio') . '</p>
                            ',
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'column_size_left',
            'label'    => esc_html__('Left', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'normal',
            'priority' => 15,
            'choices'  => [
                'normal' => esc_html__('Normal', 'municipio'),
                'large'  => esc_html__('Large', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'column_size_right',
            'label'    => esc_html__('Right', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'normal',
            'priority' => 20,
            'choices'  => [
                'normal' => esc_html__('Normal', 'municipio'),
                'large'  => esc_html__('Large', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
        ]);
    }
}
