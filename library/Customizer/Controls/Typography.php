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

    public function getFontVariantCss()
    {
        return [
            ':root' => [
                [
                    'property' =>  '--font-weight-base',
                    'value' => get_theme_mod('typography_base', [])['variant'] ?? 400
                ],
                [
                    'property' =>  '--font-weight-heading',
                    'value' => get_theme_mod('typography_heading', [])['variant'] ?? get_theme_mod('typography_base', [])['variant'] ?? 400
                ]
            ]
        ];
    }
}
