<?php

namespace Municipio\Customizer\Sections;

class Quicklinks
{
    public const SECTION_ID = "municipio_customizer_section_quicklinks";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Quicklinks', 'municipio'),
            'description' => esc_html__('Quicklinks settings.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'radio',
          'settings'    => 'quicklinks_background_type',
          'label'       => esc_html__('Select background type', 'municipio'),
          'description' => esc_html__('Select if you want to use one of the predefined colors, or select one freely.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'default',
          'priority'    => 5,
          'choices'     => [
            'default' => esc_html__( 'Predefined colors', 'municipio'),
            'hex' => esc_html__('Custom color', 'municipio'),
          ]
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'quicklinks_custom_background',
          'label'       => esc_html__('Custom background color', 'municipio'),
          'description' => esc_html__('Choose a background color for the quicklinks section of the page.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => '#ffffff',
          'output'      => [
            'type' => 'controller'
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
          'label'       => esc_html__('Predefined background color', 'municipio'),
          'description' => esc_html__('Choose a background color for the quicklinks section of the page.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'primary' => esc_html__('Primary', 'municipio'),
              'secondary' => esc_html__('Secondary', 'municipio'),
              'tertiary' => esc_html__('Tertiary', 'municipio'),
          ],
          'output' => [
              'type' => 'modifier',
              'context' => ['site.quicklinks'],
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
          'settings'    => 'quicklinks_sticky',
          'label'       => esc_html__('Sticky', 'municipio'),
          'description' => esc_html__('Adjust how the quicklinks menu should behave when the user scrolls trough the page. This option should not be used in combination with a sticky header.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'sticky' => esc_html__('Stick to top', 'municipio'),
          ],
          'output' => [
              'type' => 'modifier',
              'context' => ['site.quicklinks'],
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_color',
          'label'       => esc_html__('Text color', 'municipio'),
          'description' => esc_html__('Select a font/text color to use.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => '',
          'priority'    => 10,
          'choices'     => [
              '' => esc_html__('Default', 'municipio'),
              'text-white' => esc_html__('White', 'municipio'),
              'text-black' => esc_html__('Black', 'municipio'),
              'text-primary' => esc_html__('Primary', 'municipio'),
              'text-secondary' => esc_html__('Secondary', 'municipio'),
              'text-tertiary' => esc_html__('Tertiary', 'municipio'),
          ],
          'output' => [
              'type' => 'modifier',
              'context' => ['site.quicklinks'],
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'select',
          'settings'    => 'quicklinks_location',
          'label'       => esc_html__('Location', 'municipio'),
          'description' => esc_html__('Quicklinks location.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'frontpage',
          'priority'    => 10,
          'choices'     => [
              'frontpage' => esc_html__('Front page', 'municipio'),
              'everywhere' => esc_html__('All pages', 'municipio'),
          ],
          'output' => [
              'type' => 'controller',
          ],
        ]);

        /*

        $this->migrateThemeMod('quicklinks', 'quicklinks_background_type', 'field_61570dd479d9b');
        $this->migrateThemeMod('quicklinks', 'quicklinks_custom_background', 'field_61570e6979d9c');
        $this->migrateThemeMod('quicklinks', 'quicklinks_background', 'field_6123844e0f0bb');
        $this->migrateThemeMod('quicklinks', 'quicklinks_color', 'field_6127571bcc76e');
        $this->migrateThemeMod('quicklinks', 'quicklinks_sticky', 'field_61488b616937c');
        $this->migrateThemeMod('quicklinks', 'quicklinks_location', 'field_61488c4f6b4fd');
        
        */

    }
}
