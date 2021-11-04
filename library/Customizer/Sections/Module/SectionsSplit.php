<?php

namespace Municipio\Customizer\Sections\Module;

class SectionsSplit
{
    public const SECTION_ID = "municipio_customizer_section_mod_sections_split";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Sections Split', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
            'active_callback' => function() {
              return post_type_exists('mod-section-split');
            }
        ));

        \Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'mod_section_split_modifier',
            'label'       => esc_html__('Section Split', 'municipio'),
            'section'     => self::SECTION_ID,
            'default'     => 'none',
            'priority'    => 10,
            'choices'     => [
                'none' => esc_html__('None', 'municipio'),
                'highlight' => esc_html__('Highlight', 'municipio'),
            ],
            'output' => [
                'type' => 'modifier',
                'context' => ['sectionsSplit', 'module.sections.split'],
            ],
        ]);

    }
}
