<?php

namespace Municipio\Theme;

class Enqueue
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'style'));
        add_action('wp_enqueue_scripts', array($this, 'script'));
    }

    public function style()
    {
        wp_register_style('hbg-prime', 'http://helsingborg-stad.github.io/styleguide-web/css/hbg-prime.min.css', '', '1.0.0');
        wp_enqueue_style('hbg-prime');
    }

    public function script()
    {
    }
}
