<?php

namespace Municipio\Customizer\Panels;

class Design
{
    public function __construct(string $panelID)
    {
        new \Municipio\Customizer\Sections\Logo($panelID);
        new \Municipio\Customizer\Sections\General($panelID);
        new \Municipio\Customizer\Sections\Colors($panelID);
        new \Municipio\Customizer\Sections\Typography($panelID);
        new \Municipio\Customizer\Sections\Width($panelID);
        new \Municipio\Customizer\Sections\Borders($panelID);
        new \Municipio\Customizer\Sections\Radius($panelID);
        new \Municipio\Customizer\Sections\Padding($panelID);
        new \Municipio\Customizer\Sections\Shadow($panelID);
        new \Municipio\Customizer\Sections\Search($panelID);
    }
}
