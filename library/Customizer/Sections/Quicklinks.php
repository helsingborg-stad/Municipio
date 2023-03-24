<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer as Customizer;
use Kirki as Kirki;
use Municipio\Helper\KirkiSwatches as KirkiSwatches;

class Quicklinks
{
    public function __construct(string $sectionID)
    {
        Kirki::add_field(Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'quicklinks_appearance_type',
          'label'       => esc_html__('Appearance', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined appearance, or customize freely.', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__('Predefined appearance', 'municipio'),
            'custom' => esc_html__('Custom appearance', 'municipio'),
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance',
              'operator' => '===',
              'value'    => '',
            ]
          ],
        ]);

          Kirki::add_field(Customizer::KIRKI_CONFIG, [
          'type'        => 'multicolor',
          'settings'    => 'quicklinks_custom_colors',
          'label'       => esc_html__('Custom colors', 'municipio'),
          'section'     => $sectionID,
          'priority'    => 10,
          'transport' => 'auto',
          'choices'     => [
            'background'    => esc_html__('Background', 'municipio'),
            'sticky-background'    => esc_html__('Sticky background', 'municipio'),
            'text-color'    => esc_html__('Text background', 'municipio'),
            'icon-color'    => esc_html__('Sticky background', 'municipio'),
            'icon-background-color'    => esc_html__('Sticky background', 'municipio'),
          ],
          'default' => [
            'background'            => '#fff',
            'sticky-background'     => '#fff',
            'text-color'            => '#000',
            'icon-color'            => '#000',
            'icon-background-color' => '#fff',
          ],
          'palettes' => KirkiSwatches::getColors(),
          'output' => [
            [
                'choice'    => 'background',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-background-color'
            ],
            [
                'choice'    => 'sticky-background',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-sticky-background-color'
            ],
            [
                'choice'    => 'text-color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-text-color'
            ],
            [
                'choice'    => 'icon-color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-icon-color'
            ],
            [
                'choice'    => 'icon-background-color',
                'element'   => '.s-nav-fixed',
                'property'  => '--c-quicklinks-icon-background-color'
            ],
          ],
          'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance_type',
              'operator' => '===',
              'value'    => 'custom',
            ]
            ],
            'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance_type',
              'operator' => '===',
              'value'    => 'custom',
            ]
          ],
      ]);

      Kirki::add_field(Customizer::KIRKI_CONFIG, [
        'type'        => 'slider',
        'settings'    => 'quicklinks_gap',
        'label'       => esc_html__('Amount of gap between', 'municipio'),
        'section'     => $sectionID,
        'transport'   => 'auto',
        'default'     => 2,
        'choices'     => [
            'min'  => 1,
            'max'  => 10,
            'step' => 1,
        ],
        'output' => [
            [
              'element' => '.s-nav-fixed',
              'property' => '--c-quicklinks-gap'
            ]
        ],
         'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance_type',
              'operator' => '===',
              'value'    => 'custom',
            ]
          ],
      ]);

      Kirki::add_field(Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_alignment',
          'label'       => esc_html__('Alignment', 'municipio'),
          'section'     => $sectionID,
          'default'     => 'space-evenly',
          'choices'     => [
              'space-evenly' => esc_html__('Gap between', 'municipio'),
              'center' => esc_html__('Center', 'municipio'),
          ],
          'output' => [
              [
                  'property' => '--c-quicklinks-alignment',
                  'element' => '.s-nav-fixed'
              ],
          ],
           'active_callback'  => [
            [
              'setting'  => 'quicklinks_appearance_type',
              'operator' => '===',
              'value'    => 'custom',
            ]
          ],
      ]);

        Kirki::add_field(Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_color_scheme',
          'label'       => esc_html__('Color scheme', 'municipio'),
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
              'setting'  => 'quicklinks_appearance_type',
              'operator' => '===',
              'value'    => 'default',
            ]
          ],
        ]);

        Kirki::add_field(Customizer::KIRKI_CONFIG, [
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

        Kirki::add_field(Customizer::KIRKI_CONFIG, [
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

        Kirki::add_field(Customizer::KIRKI_CONFIG, [
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
        
        Kirki::add_field(Customizer::KIRKI_CONFIG, [
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
