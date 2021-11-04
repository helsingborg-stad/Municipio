<?php

namespace Municipio\Customizer\Sections\Module;

class Index
{
    public const SECTION_ID = "municipio_customizer_section_mod_index";

    public function __construct($panelID)
    {
        \Kirki::add_section(self::SECTION_ID, array(
            'title'       => esc_html__('Index', 'municipio'),
            'panel'          => $panelID,
            'priority'       => 160,
            'active_callback' => function() {
              return post_type_exists('mod-index');
            }
        ));

    }
}
