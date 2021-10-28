<?php

namespace Municipio\Customizer\Panels;

class Example
{
    public const PANEL_ID = "municipio_customizer_panel_example";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 10,
            'title'       => esc_html__('Example panel', 'municipio'),
            'description' => esc_html__('A example panel', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\Example(self::PANEL_ID);
    }
}
