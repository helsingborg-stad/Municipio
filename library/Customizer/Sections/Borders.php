<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Borders
{
    public const SECTION_ID = "municipio_customizer_section_border";

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_divider',
            'label'     => esc_html__('Divider', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 1,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-divider',
                    'units'    => 'px'
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_highlight',
            'label'     => esc_html__('Highlight', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 4,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-hightlight',
                    'units'    => 'px'
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_card',
            'label'     => esc_html__('Card', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 0,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-card',
                    'units'    => 'px'
                ]
            ],
        ]);

        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_outline',
            'label'     => esc_html__('Outline', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 1,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-outline',
                    'units'    => 'px'
                ]
            ],
        ]);


        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_button',
            'label'     => esc_html__('Button', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 2,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-button',
                    'units'    => 'px'
                ]
            ],
        ]);


        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_input',
            'label'     => esc_html__('Input', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 1,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-input',
                    'units'    => 'px'
                ]
            ],
        ]);
    }
}
