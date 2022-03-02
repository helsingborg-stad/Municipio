<?php

namespace Municipio\Customizer\Panels;

class Archive
{
    public const PANEL_ID = "municipio_customizer_panel_archive";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Archive Apperance', 'municipio'),
            'description' => esc_html__('Manage apperance options on archives.', 'municipio'),
        ));

        //new \Municipio\Customizer\Sections\Header(self::PANEL_ID);
    }
}
