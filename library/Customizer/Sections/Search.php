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
                'quicklinks' => __('Quicklinks menu', 'municipio'),
            ],
            'output' => [[
                'type' => 'controller',
                'as_object' => false,
            ]
            ]
        )));


        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'        => 'select',
            'settings'    => 'hero_search_position',
            'label'       => esc_html__('Hero search position', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'centered',
            'priority'    => 10,
            'choices'     => [
                'top' => esc_html__('Top', 'municipio'),
                'centered' => esc_html__('Centered', 'municipio'),
                'bottom' => esc_html__('Bottom', 'municipio'),

            ],
            'active_callback' => [
                [
                    'setting'  => 'search_display',
                    'operator' => 'in',
                    'value'    => 'hero',
                ]
            ],
            'output' => [
                [
                    'type' => 'modifier',
                    'context' => ['hero.search.form'],
                ]
            ],
        ]);
    }
}
