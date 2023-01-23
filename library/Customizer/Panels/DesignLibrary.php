<?php

namespace Municipio\Customizer\Panels;

class DesignLibrary
{
    public const PANEL_ID = "municipio_customizer_panel_designlib";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 1000,
            'title'       => esc_html__('Design Library', 'municipio'),
            'description' => esc_html__('Select a design made by other municipio users.', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\LoadDesign(self::PANEL_ID);
    }
}
