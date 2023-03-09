<?php

namespace Municipio\Customizer\Sections;
use Municipio\Customizer as Customizer;
use Kirki as Kirki;

class Siteselector
{
    public function __construct(string $sectionID)
    {
        Kirki::add_field(Customizer::KIRKI_CONFIG, [
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
          ]
        ]);

        \Kirki::add_field(
          \Municipio\Customizer::KIRKI_CONFIG,
          [
              'type'        => 'multicolor',
              'settings'    => 'color_border',
              'label'       => esc_html__('Custom colors', 'municipio'),
              'section'     => $sectionID,
              'priority'    => 10,
              'transport' => 'auto',
              'choices'     => [
                  'background'    => esc_html__('Background', 'municipio'),
                  'contrasting'    => esc_html__('Contrasting', 'municipio'),
                  'background_active'    => esc_html__('Background (active)', 'municipio'),
                  'contrasting_active'    => esc_html__('Contrasting (active)', 'municipio'),
              ],
              'default'     => [
                  'background'    => '#eee',
                  'contrasting'    => '#000',
                  'background_active' => '#eee',
                  'contrasting_active' => '#eee'
              ],
              'output' => [
                  [
                      'choice'    => 'background',
                      'element'   => ':root',
                      'property'  => '--color-border-divider',
                  ],
                  [
                      'choice'    => 'contrasting',
                      'element'   => ':root',
                      'property'  => '--color-border-outline',
                  ],
                  [
                    'choice'    => 'background_active',
                    'element'   => ':root',
                    'property'  => '--color-border-outline',
                  ],
                  [
                    'choice'    => 'contrasting_active',
                    'element'   => ':root',
                    'property'  => '--color-border-outline',
                  ],
              ],
              'active_callback'  => [
                [
                  'setting'  => 'siteselector_color_source',
                  'operator' => '===',
                  'value'    => 'custom',
                ]
              ],
          ]
      );

      Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        'type'        => 'select',
        'settings'    => 'siteselector_color_scheme',
        'label'       => esc_html__('Color scheme', 'municipio'),
        'description' => esc_html__('Select color scheme to use for this component.', 'municipio'),
        'section'     => $sectionID,
        'default'     => '',
        'priority'    => 10,
        'choices'     => [
          'primary'   => esc_html__('Primary', 'municipio'),
          'secondary' => esc_html__('Secondary', 'municipio')
        ],
        'output' => [
          [
            'type' => 'modifier',
            'context' => ['site.quicklinks']
          ]
        ],
        'active_callback'  => [
          [
            'setting'  => 'siteselector_color_source',
            'operator' => '===',
            'value'    => 'default',
          ]
        ],
      ]);

      Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        'type'        => 'select',
        'settings'    => 'siteselector_border_radius',
        'label'       => esc_html__('Text color', 'municipio'),
        'description' => esc_html__('Select a font/text color to use.', 'municipio'),
        'section'     => $sectionID,
        'default'     => 'pill',
        'priority'    => 10,
        'choices'     => [
          'xs' => esc_html__('Extra small', 'municipio'),
          'sm' => esc_html__('Small', 'municipio'),
          'md' => esc_html__('Medium', 'municipio'),
          'lg' => esc_html__('Large', 'municipio'),
          'pill' => esc_html__('Pill', 'municipio')
        ],
        'output' => [
          [
            'type' => 'modifier',
            'context' => ['site.quicklinks']
          ]
        ]
      ]);

    }
}
