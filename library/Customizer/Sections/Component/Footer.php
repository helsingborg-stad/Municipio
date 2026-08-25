<?php

namespace Municipio\Customizer\Sections\Component;

class Footer
{
    public function __construct(string $sectionID)
    {
        new FooterMain($sectionID);
        new FooterSub($sectionID);
    }
}