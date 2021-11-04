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

    }
}
