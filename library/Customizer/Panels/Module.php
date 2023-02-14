<?php

namespace Municipio\Customizer\Panels;

class Module
{
    public function __construct(string $panelID)
    {
        new \Municipio\Customizer\Sections\Module\Contacts($panelID);
        new \Municipio\Customizer\Sections\Module\Index($panelID);
        new \Municipio\Customizer\Sections\Module\Inlay($panelID);
        new \Municipio\Customizer\Sections\Module\LocalEvent($panelID);
        new \Municipio\Customizer\Sections\Module\Map($panelID);
        new \Municipio\Customizer\Sections\Module\Posts($panelID);
        new \Municipio\Customizer\Sections\Module\Script($panelID);
        new \Municipio\Customizer\Sections\Module\SectionsSplit($panelID);
        new \Municipio\Customizer\Sections\Module\Text($panelID);
        new \Municipio\Customizer\Sections\Module\Video($panelID);

    }
}
