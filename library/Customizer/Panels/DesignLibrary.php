<?php

namespace Municipio\Customizer\Panels;

use Municipio\Customizer\CustomizerPanel;

class DesignLibrary
{
    public const PANEL_ID = "municipio_customizer_panel_designlib";

    public function __construct()
    {
        CustomizerPanel::create()
            ->setID(self::PANEL_ID)
            ->setPriority(1000)
            ->setTitle(esc_html__('Design Library', 'municipio'))
            ->setDescription(esc_html__('Select a design made by other municipio users.', 'municipio'))
            ->register();

        new \Municipio\Customizer\Sections\LoadDesign(self::PANEL_ID);
    }
}
