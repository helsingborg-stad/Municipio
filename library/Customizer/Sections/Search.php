<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;
use Kirki\Field\Multicheck;

class Search
{
    public const SECTION_ID = "municipio_customizer_section_search";

    public function __construct($panelID)
    {
        Kirki::add_section(self::SECTION_ID, array(
            'title'         => esc_html__('Search', 'municipio'),
            'panel'          => $panelID,
        ));

        Kirki::add_field(new Multicheck(array(
            'settings'  => 'search_display',
            'section'     => self::SECTION_ID,
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
