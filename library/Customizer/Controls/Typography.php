<?php

namespace Municipio\Customizer\Controls;

class Typography
{
    public function __construct()
    {
        add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_dynamic_css', array($this, 'fixCssVariants'));
    }

    public function fixCssVariants($css) 
    {
        return $css;
    }

}