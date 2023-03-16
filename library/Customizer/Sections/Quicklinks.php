<?php

namespace Municipio\Customizer\Sections;

class Quicklinks
{
    public function __construct(string $sectionID)
    {

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'quicklinks_background_type',
          'label'       => esc_html__('Select background type', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined colors, or select one freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__( 'Predefined colors', 'municipio'),
            'hex' => esc_html__('Custom color', 'municipio'),
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance',
              'operator' => '===',
              'value'    => '',
            ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'quicklinks_custom_background',
          'label'       => esc_html__('Custom background color', 'municipio'),
          'description' => esc_html__('Choose a background color for the quicklinks section of the page.', 'municipio'),
          'section'     => $sectionID,
          'default'     => '#ffffff',
          'output' => [
            ['type' => 'controller']
          ],
          'alpha' => 1,
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_background_type',
              'operator' => '===',
              'value'    => 'hex',
            ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        'type'        => 'multicolor',
        'settings'    => 'quicklinks_colors',
        'label'       => esc_html__('colors', 'municipio'),
        'section'     => $sectionID,
        'priority'    => 10,
        'transport'   => 'auto',
        'alpha'       => true,
        'choices'     => [
            'text_color' => esc_html__('Text color', 'municipio'),
            'icon_color' => esc_html__('Icon color', 'municipio'),
            'icon_background_color' => esc_html__('Icon background color', 'municipio'),
        ],
        'default' => [
            'text_color'                => '#000',
            'icon_color'                => '#000',
            'icon_background_color'     => '#fff',
        ],
        'output' => [
            [
                'choice'    => 'text_color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-text-color',
            ],
            [
                'choice'    => 'icon_color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-icon-color',
            ],
            [
                'choice'    => 'icon_background_color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-icon-background-color',
            ],
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_background_type',
              'operator' => '===',
              'value'    => 'hex',
            ]
          ],
      ]); 

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_background',
          'label'       => esc_html__('Color scheme', 'municipio'),
          'description' => esc_html__('Set color scheme to use for this component', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'primary',
          'priority'    => 10,
          'choices'     => [
              'primary' => esc_html__('Primary', 'municipio'),
              'secondary' => esc_html__('Secondary', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['site.quicklinks']
              ]
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_background_type',
              'operator' => '===',
              'value'    => 'default',
            ]
          ],
        ]);

      \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
        'type'        => 'slider',
        'settings'    => 'nav_quicklinks_gap',
        'label'       => esc_html__('Amount of gap between', 'municipio'),
        'section'     => $sectionID,
        'priority'    => 10,
        'transport'   => 'auto',
        'default'     => 2,
        'choices'     => [
            'min'  => 1,
            'max'  => 10,
            'step' => 1,
        ],
        'output' => [
          [
              'property' => '--c-nav-quicklinks-gap',
              'element' => '.s-nav-fixed'
          ]
        ],
      ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_sticky',
          'label'       => esc_html__('Sticky', 'municipio'),
          'description' => esc_html__('Adjust how the quicklinks menu should behave when the user scrolls trough the page. This option should not be used in combination with a sticky header.', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'sticky' => esc_html__('Stick to top', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['site.quicklinks']
              ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_location',
          'label'       => esc_html__('Location', 'municipio'),
          'description' => esc_html__('Quicklinks location.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'frontpage',
          'priority'    => 10,
          'choices'     => [
              'frontpage' => esc_html__('Front page', 'municipio'),
              'everywhere' => esc_html__('All pages', 'municipio'),
          ],
          'output' => [
            ['type' => 'controller']
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_direction',
          'label'       => esc_html__('Quicklinks item direction', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'choices'     => [
              '' => esc_html__('Row', 'municipio'),
              'column' => esc_html__('Column', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['site.quicklinks']
              ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_overflow',
          'label'       => esc_html__('Overflow top', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'overflow-top' => esc_html__('Overflow top', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['site.quicklinks']
              ]
          ],
        ]);

    }
}
