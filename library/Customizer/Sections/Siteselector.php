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
          'settings'    => 'siteselector_background_type',
          'label'       => esc_html__('Select background type', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined colors, or select one freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__('Predefined colors', 'municipio'),
            'hex'     => esc_html__('Custom color', 'municipio'),
          ],
          'active_callback'  => [
            [
              'setting'  => 'siteselector_appearance',
              'operator' => '===',
              'value'    => '',
            ]
          ],
        ]);

        Kirki::add_field(Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'siteselector_background',
          'label'       => esc_html__('Custom background color', 'municipio'),
          'description' => esc_html__('Choose a background color for the siteselector navigation.', 'municipio'),
          'section'     => $sectionID,
          'default'     => '#ffffff',
          'output' => [
              'element'   => ':root',
              'property'  => '--c-button-primary-color',
          ],
          'alpha' => 1,
          
        ]);


        \Kirki::add_field(
          \Municipio\Customizer::KIRKI_CONFIG,
          [
              'type'        => 'multicolor',
              'settings'    => 'color_border',
              'label'       => esc_html__('Border colors', 'municipio'),
              'section'     => $sectionID,
              'priority'    => 10,
                  'transport' => 'auto',
              'choices'     => [
                  'divider'    => esc_html__('Divider', 'municipio'),
                  'outline'    => esc_html__('Outline', 'municipio'),
              ],
              'default'     => [
                  'divider'    => 'rgba(0,0,0,0.1)',
                  'outline'    => 'rgba(0,0,0,0.1)',
              ],
              'output' => [
                  [
                      'choice'    => 'divider',
                      'element'   => ':root',
                      'property'  => '--color-border-divider',
                  ],
                  [
                      'choice'    => 'outline',
                      'element'   => ':root',
                      'property'  => '--color-border-outline',
                  ],
                ],
                'active_callback'  => [
                  [
                    'setting'  => 'siteselector_background_type',
                    'operator' => '===',
                    'value'    => 'hex',
                  ]
                ],
          ]
      );











        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_background',
          'label'       => esc_html__('Predefined background color', 'municipio'),
          'description' => esc_html__('Choose a background color for the quicklinks section of the page.', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'primary' => esc_html__('Primary', 'municipio'),
              'secondary' => esc_html__('Secondary', 'municipio'),
              'transparent' => esc_html__('Transparent', 'municipio')

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
          'type'        => 'select',
          'settings'    => 'quicklinks_transparent_sticky_color',
          'label'       => esc_html__('Background color sticky scroll', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'choices'     => [
              '' => esc_html__('None', 'municipio'),
              'primary-scroll-background' => esc_html__('Primary', 'municipio'),
              'secondary-scroll-background' => esc_html__('Secondary', 'municipio'),
              'white-scroll-background' => esc_html__('White', 'municipio'),
              'black-scroll-background' => esc_html__('Black', 'municipio'),
          ],
          'output' => [
              [
                'type' => 'modifier',
                'context' => ['site.quicklinks']
              ]
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_sticky',
              'operator' => '===',
              'value'    => 'sticky',
            ],
            [
              'setting'  => 'quicklinks_background',
              'operator' => '===',
              'value'    => 'transparent',
            ],
            [
              'setting'  => 'quicklinks_background_type',
              'operator' => '===',
              'value'    => 'default',
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
          'settings'    => 'quicklinks_color',
          'label'       => esc_html__('Text color', 'municipio'),
          'description' => esc_html__('Select a font/text color to use.', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'text-white' => esc_html__('White', 'municipio'),
              'text-black' => esc_html__('Black', 'municipio'),
              'text-primary' => esc_html__('Primary', 'municipio'),
              'text-secondary' => esc_html__('Secondary', 'municipio')
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
          'settings'    => 'quicklinks_icon_color',
          'label'       => esc_html__('Icon color', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'primary-icon' => esc_html__('Primary', 'municipio'),
              'secondary-icon' => esc_html__('Secondary', 'municipio'),
              'white-icon' => esc_html__('White', 'municipio'),
              'black-icon' => esc_html__('Black', 'municipio'),
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
          'settings'    => 'quicklinks_icon_background_color',
          'label'       => esc_html__('Icon background color', 'municipio'),
          'section'     => $sectionID,
          'default'     => '',
          'choices'     => [
              '' => esc_html__('None', 'municipio'),
              'primary-icon-background' => esc_html__('Primary', 'municipio'),
              'secondary-icon-background' => esc_html__('Secondary', 'municipio'),
              'white-icon-background' => esc_html__('White', 'municipio'),
              'black-icon-background' => esc_html__('Black', 'municipio'),
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
