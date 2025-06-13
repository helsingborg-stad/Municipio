<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Tags
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'tags_style_settings',
            'label'       => esc_html__('Tag styling', 'municipio'),
            'description' => esc_html__('Which styling to use for tags.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''     => esc_html__('Default', 'municipio'),
                'pill' => esc_html__('Pill', 'municipio')
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'tagsStyle',
                    'context' => [
                        [
                            'context'  => 'component.tags',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'tags_markings_style',
            'label'       => esc_html__('Tag icon', 'municipio'),
            'description' => esc_html__('Icon to prepend tags with.', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''                => esc_html__("Default", 'municipio'),
                'taxonomy-colors' => esc_html__('Dot', 'municipio')
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'tagsMarker',
                    'context' => [
                        [
                            'context'  => 'component.tags',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'tags_compress',
            'label'       => esc_html__('Compress tags', 'municipio'),
            'description' => esc_html__('If set to 0, no compression will take place. Any other value will only show selected amount of tags.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 0,
            'choices'     => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => "compress",
                    'context' => [
                        [
                            'context'  => 'component.tags',
                            'operator' => '==',
                        ],
                    ]
                ]
            ],
        ]);
    }
}
