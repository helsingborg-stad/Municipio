<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Datebadge
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'datebadge_color_settings',
            'label'       => esc_html__('Datebadge color', 'municipio'),
            'description' => esc_html__('Which color the datebadges should appear in.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'light',
            'priority'    => 10,
            'choices'     => [
                'light'     => esc_html__('Light', 'municipio'),
                'dark' => esc_html__('Dark', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'color',
                    'context' => [
                        [
                            'context'  => 'component.datebadge',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
    }
}
