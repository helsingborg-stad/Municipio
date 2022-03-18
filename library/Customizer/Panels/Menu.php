<?php

namespace Municipio\Customizer\Panels;

class Menu
{
    public const PANEL_ID = "municipio_customizer_panel_menu";

    public function __construct()
    {
        \Kirki::add_panel('nav_menus', array(
            'priority'    => 1000,
            'title'       => esc_html__('Example panel', 'municipio'),
            'description' => esc_html__('A example panel', 'municipio'),
            'section' => 'nav_menus'
        ));

        new \Municipio\Customizer\Sections\Menu('nav_menus');
    }
}
