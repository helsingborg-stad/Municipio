<?php

namespace Municipio\Customizer\Controls;

class Typography
{
    public function __construct()
    {
        add_filter('kirki_' . \Municipio\Customizer::KIRKI_CONFIG . '_dynamic_css', array($this, 'fixCssVariants'));
    }

    // Define array
    public $typographyFields = ([]);
    
    public function fixCssVariants($styles)
    {
        foreach ($this->getFontVariantCss() as $selector => $outputs) {
            $cssFromOutput = implode(' ', array_map(function ($output) {
                return "{$output['property']} : {$output['value']};";
            }, $outputs));

            $styles .= "
                {$selector}{
                    {$cssFromOutput}
                }
            ";
        }

        return $styles;
    }



    public function getFontVariantCss()
    {
        // Populate with kirki_field_init (not working yet)
        // add_action( 'kirki_field_init', [$this->typographyFields] = fieldObject );
        $typographyFields = array_fill(0, 3, ":selector { property: value; }");


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
