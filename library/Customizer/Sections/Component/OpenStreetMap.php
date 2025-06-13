<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class OpenStreetMap
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'map_style',
            'label'       => esc_html__('Map style', 'municipio'),
            'description' => esc_html__('Which style to use for Open Street Maps.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'default',
            'priority'    => 10,
            'choices'     => [
                'default' => esc_html__('Default', 'municipio'),
                'pale'    => esc_html__('Pale', 'municipio'),
                'dark'    => esc_html__('Dark', 'municipio'),
                'color'   => esc_html__('Colorful', 'municipio')
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'mapStyle',
                    'context' => [
                        [
                            'context'  => 'component.openstreetmap',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
            ]);

        KirkiField::addField([
            'type'        => 'text',
            'settings'    => 'map_start_lat_lng',
            'label'       => esc_html__('Map start center', 'municipio'),
            'description' => esc_html__('Which latitude and longitude to center Open Street Maps on as default.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '56.04388993324803, 12.695627213683235',
            'priority'    => 10,
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'mapStartLatLng',
                    'context' => [
                        [
                            'context'  => 'component.openstreetmap',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
        KirkiField::addField([
            'type'        => 'number',
            'settings'    => 'map_start_zoom',
            'label'       => esc_html__('Map start zoom', 'municipio'),
            'description' => esc_html__('Which zoom level to use on Open Street Maps on as default.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 14,
            'priority'    => 10,
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'mapStartZoom',
                    'context' => [
                        [
                            'context'  => 'component.openstreetmap',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
    }
}
