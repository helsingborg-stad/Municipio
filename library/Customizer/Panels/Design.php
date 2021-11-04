<?php

namespace Municipio\Customizer\Panels;

class Design
{
    public const PANEL_ID = "municipio_customizer_panel_design";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('General Apperance', 'municipio'),
            'description' => esc_html__('Manage site general design options.', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\General(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Colors(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Typography(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Width(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Radius(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Overlay(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Padding(self::PANEL_ID);
    }
}
