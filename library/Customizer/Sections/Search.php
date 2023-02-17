<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;
use Kirki\Field\Multicheck;

class Search
{
    public function __construct(string $sectionID)
    {
        Kirki::add_field(new Multicheck(array(
            'settings'  => 'search_display',
            'section'     => $sectionID,
            'label' => __('Show search', 'municipio'),
            'default' => [],
            'choices' => [
                'hero' => __('Hero on frontpage', 'municipio'),
                'header_sub' => __('Header on sub pages', 'municipio'),
                'header' => __('Header on frontpage', 'municipio'),
                'mainmenu' => __('Option in main menu', 'municipio'),
                'mobile' => __('Option in mobile menu', 'municipio'),
                'hamburger_menu' => __('Hamburger menu', 'municipio'),
            ],
            'output' => [[
                'type' => 'controller',
                'as_object' => false,
            ]
            ]
        )));
    }
}
