<?php

namespace Municipio\Customizer\Sections;

class Hero
{
    public const SECTION_ID = "municipio_customizer_section_hero";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Hero', 'municipio'),
            'description' => esc_html__('Specific settings for the hero component.', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'switch',
          'settings'    => 'hero_overlay_enable',
          'label'       => esc_html__('Enable hero-overlay customization.', 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => false,
          'priority'    => 10,
          'choices'     => [
            true  => esc_html__('Enable', 'municipio'),
            false => esc_html__('Disable', 'municipio'),
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'hero_overlay_neutral',
          'label'       => esc_html__('Neutral overlay', 'municipio'),
          'description' => esc_html__("Choose a neutral overlaycolor for hero use.", 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'rgba(0,0,0,.6)',
          'output'      => [
            'element'   => ':root',
            'property'  => '--hero-overlay-color--neutral'
          ],
          'choices'     => [
            'alpha' => true,
          ],
          'active_callback'  => [
            [
              'setting'  => 'hero_overlay_enable',
              'operator' => '==',
              'value'    => true,
            ]
          ],
        ]);

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
          'type'        => 'color',
          'settings'    => 'hero_overlay_vibrant',
          'label'       => esc_html__('Vibrant overlay', 'municipio'),
          'description' => esc_html__("Choose a vibrant overlaycolor for hero use.", 'municipio'),
          'section'     => self::SECTION_ID,
          'default'     => 'rgba(0,0,0,.6)',
          'output'      => [
            'element'   => ':root',
            'property'  => '--hero-overlay-color--vibrant'
          ],
          'choices'     => [
            'alpha' => true,
          ],
          'active_callback'  => [
            [
              'setting'  => 'hero_overlay_enable',
              'operator' => '==',
              'value'    => true,
            ]
          ],
        ]);
    }
}
