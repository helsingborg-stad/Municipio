<?php

namespace Municipio\Customizer\Panels;

class Menu
{
    public const PANEL_ID = "nav_menus";

    public function __construct()
    {
        new \Municipio\Customizer\Sections\Menu(self::PANEL_ID);
    }
}
