<?php

namespace Municipio\Customizer\Sections;

class Quicklinks
{
    public function __construct(string $sectionID)
    {
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
