<?php

namespace Municipio\Customizer\Panels;

class Component
{
    public function __construct(string $panelID)
    {
        // new \Municipio\Customizer\Sections\Header($panelID);
        // new \Municipio\Customizer\Sections\Quicklinks($panelID);
        // new \Municipio\Customizer\Sections\Button($panelID);
        // new \Municipio\Customizer\Sections\HamburgerMenu($panelID);
        // new \Municipio\Customizer\Sections\Slider($panelID);
        new \Municipio\Customizer\Sections\Footer($panelID);
        new \Municipio\Customizer\Sections\Divider($panelID);
        new \Municipio\Customizer\Sections\Hero($panelID);
        new \Municipio\Customizer\Sections\Field($panelID);
        
        // new \Municipio\Customizer\Sections\Card(self::PANEL_ID);
        // new \Municipio\Customizer\Sections\Collection(self::PANEL_ID);
        // new \Municipio\Customizer\Sections\Field(self::PANEL_ID);
    }
}
