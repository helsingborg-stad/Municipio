<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Divider
{
    public function __construct($sectionID)
    {
        KirkiField::addField([
            'type' => 'select',
            'settings' => 'divider_border_style',
            'label' => esc_html__('Border style', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'default' => 'solid',
            'choices' => [
                'solid' => __('Solid', 'municipio'),
                'dotted' => __('Dotted', 'municipio'),
                'dashed' => __('Dashed', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'style',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'color',
            'settings' => 'divider_color_text',
            'label' => esc_html__('Text color', 'municipio'),
            'section' => $sectionID,
            'priority' => 10,
            'transport' => 'auto',
            'alpha' => true,
            'default' => 'rgba(255, 255, 255, 0)',
            'active_callback' => [
                [
                    'setting' => 'divider_custom_font',
                    'operator' => '==',
                    'value' => true,
                ],
            ],
            'output' => [
                [
                    'choice' => 'text',
                    'element' => ':root',
                    'property' => '--c-divider-color-text',
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'select',
            'settings' => 'divider_title_alignment',
            'label' => esc_html__('Text alignment', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'default' => 'center',
            'choices' => [
                'left' => __('Left', 'municipio'),
                'center' => __('Center', 'municipio'),
                'right' => __('Right', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'align',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
        ]);

        KirkiField::addField([
            'type' => 'switch',
            'settings' => 'divider_title_frame',
            'label' => esc_html__('Wrap title in frame', 'municipio'),
            'section' => $sectionID,
            'transport' => 'refresh',
            'default' => true,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                [
                    'type' => 'component_data',
                    'dataKey' => 'frame',
                    'context' => [
                        [
                            'context' => 'component.divider',
                            'operator' => '==',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
