<?php

namespace Municipio\Customizer\Panels;

class ContentType
{
    public const PANEL_ID = "municipio_customizer_panel_contenttype";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 1000,
            'title'       => esc_html__('Content Types', 'municipio'),
            'description' => esc_html__('Manage content types and their settings.', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\ContentType\ContentType(self::PANEL_ID);
    }
}
