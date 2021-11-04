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

        new \Municipio\Customizer\Sections\Module\Contacts(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Index(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Inlay(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\LocalEvent(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Map(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Posts(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Script(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\SectionsSplit(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Text(self::PANEL_ID);
        new \Municipio\Customizer\Sections\Module\Video(self::PANEL_ID);

    }
}
