<?php

namespace Municipio\Customizer\Sections\Module;

class Text
{
    public const SECTION_ID = "municipio_customizer_section_mod_text";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Text', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
            'active_callback' => function() {
              return post_type_exists('mod-text');
            }
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'mod_text_modifier',
            'label'       => esc_html__('List', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'none',
            'priority'    => 10,
            'choices'     => [
              'none' => esc_html__('None', 'municipio'),
              'panel' => esc_html__('Panel', 'municipio'),
              'accented' => esc_html__('Accented', 'municipio'),
              'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output' => [
                'type' => 'modifier',
                'context' => ['module.text.box'],
            ],
        ]);

    }
}
