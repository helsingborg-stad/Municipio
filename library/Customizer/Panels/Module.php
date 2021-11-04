<?php

namespace Municipio\Customizer\Panels;

class Module
{
    public const PANEL_ID = "municipio_customizer_panel_design_module";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Module Apperance', 'municipio'),
            'description' => esc_html__('Manage design options on module level.', 'municipio'),
        ));

    }
}
