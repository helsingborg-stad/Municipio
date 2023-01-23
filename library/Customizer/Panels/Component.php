<?php

namespace Municipio\Customizer\Panels;

class Component
{
    public const PANEL_ID = "municipio_customizer_panel_design_component";

    public function __construct()
    {
        \Kirki::add_panel(self::PANEL_ID, array(
            'priority'    => 120,
            'title'       => esc_html__('Component Apperance', 'municipio'),
            'description' => esc_html__('Manage design options on component level.', 'municipio'),
        ));

        new \Municipio\Customizer\Sections\Header(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Quicklinks(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Button(self::PANEL_ID);
        new \Municipio\Customizer\Sections\HamburgerMenu(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Slider(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Footer(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Divider(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Hero(self::PANEL_ID);
        new \Municipio\Customizer\Sections\ProgressBar(self::PANEL_ID);
        
        //new \Municipio\Customizer\Sections\Card(self::PANEL_ID);
        //new \Municipio\Customizer\Sections\Collection(self::PANEL_ID);
        //new \Municipio\Customizer\Sections\Field(self::PANEL_ID);
    }
}
