<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\KirkiField;

class Siteselector
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
          'type'        => 'radio',
          'settings'    => 'siteselector_color_source',
          'label'       => esc_html__('Select color source', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined colors, or select one freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__('Predefined colors', 'municipio'),
            'custom'  => esc_html__('Custom color', 'municipio'),
          ],
          'output'      => [
            [
              'type'    => 'component_data',
              'dataKey' => 'colorMode',
              'context' => [
                [
                    'context'  => 'component.siteselector',
                    'operator' => '==',
                ],
              ]
            ]
          ],
        ]);

        KirkiField::addField([
          'type'            => 'multicolor',
          'settings'        => 'custom_colors',
          'label'           => esc_html__('Custom colors', 'municipio'),
          'section'         => $sectionID,
          'priority'        => 10,
          'transport'       => 'auto',
          'choices'         => [
            'background'  => esc_html__('Background', 'municipio'),
            'contrasting' => esc_html__('Contrasting', 'municipio')
          ],
          'default'         => [
            'background'  => '#eee',
            'contrasting' => '#000'
          ],
          'output'          => [
            [
                'choice'   => 'background',
                'element'  => ':root',
                'property' => '--c-siteselector-background'
            ],
            [
                'choice'   => 'contrasting',
                'element'  => ':root',
                'property' => '--c-siteselector-contrast'
            ]
          ],
          'active_callback' => [
            [
              'setting'  => 'siteselector_color_source',
              'operator' => '===',
              'value'    => 'custom',
            ]
          ]
        ]);

        KirkiField::addField([
        'type'            => 'select',
        'settings'        => 'siteselector_color_scheme',
        'label'           => esc_html__('Color scheme', 'municipio'),
        'description'     => esc_html__('Select color scheme to use for this component.', 'municipio'),
        'section'         => $sectionID,
        'default'         => '',
        'priority'        => 10,
        'choices'         => [
          'primary'   => esc_html__('Primary', 'municipio'),
          'secondary' => esc_html__('Secondary', 'municipio')
        ],
        'output'          => [
          [
            'type'    => 'component_data',
            'dataKey' => 'color',
            'context' => [
              [
                  'context'  => 'component.siteselector',
                  'operator' => '==',
              ],
            ]
          ]
        ],
        'active_callback' => [
          [
            'setting'  => 'siteselector_color_source',
            'operator' => '===',
            'value'    => 'default',
          ]
        ],
        ]);

        KirkiField::addField([
          'type'        => 'select',
          'settings'    => 'siteselector_border_radius',
          'label'       => esc_html__('Rounded corners', 'municipio'),
          'description' => esc_html__('Select amount of border radius', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'pill',
          'priority'    => 10,
          'choices'     => [
            'xs'   => esc_html__('Extra small', 'municipio'),
            'sm'   => esc_html__('Small', 'municipio'),
            'md'   => esc_html__('Medium', 'municipio'),
            'lg'   => esc_html__('Large', 'municipio'),
            'pill' => esc_html__('Pill', 'municipio')
          ],
          'output'      => [
            [
              'type'    => 'component_data',
              'dataKey' => 'radius',
              'context' => [
                [
                    'context'  => 'component.siteselector',
                    'operator' => '==',
                ],
              ]
            ]
          ],
        ]);

        KirkiField::addField([
          'type'        => 'slider',
          'settings'    => 'siteselector_max_items',
          'label'       => esc_html__('Number of items', 'municipio'),
          'description' => esc_html__('The maximum number of items to display, before folding to a dropdown.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 3,
          'choices'     => [
              'min'  => 2,
              'max'  => 7,
              'step' => 1,
          ],
          'output'      => [
            [
              'type'    => 'component_data',
              'dataKey' => 'maxItems',
              'context' => [
                [
                    'context'  => 'component.siteselector',
                    'operator' => '==',
                ],
              ]
            ]
          ],
        ]);
    }
}
