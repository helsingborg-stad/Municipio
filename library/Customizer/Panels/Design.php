<?php

namespace Municipio\Customizer\Panels;

class Design
{
    public const PANEL_ID = "municipio_customizer_panel_design";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 10,
            'title'       => esc_html__('Design', 'municipio'),
            'description' => esc_html__('Design panel woho!!!', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\Colors(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Typography(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Shape(self::PANEL_ID);
    }
}
