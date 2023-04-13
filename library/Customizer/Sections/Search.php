<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;

class Search
{
    public function __construct(string $sectionID)
    {
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, $this->getSearchDisplayFieldAttributes($sectionID));
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, $this->getHeroSearchPositionFieldAttributes($sectionID));
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, $this->getSearchFormShapeFieldAttributes($sectionID));
    }

    public function getHeroSearchPositionFieldAttributes(string $sectionID)
    {
        return [
            'type'        => 'select',
            'settings'    => 'hero_search_position',
            'label'       => esc_html__('Hero search position', 'municipio'),
            'section'     => $sectionID,
            'default'     => $this->getHeroSearchPositionDefaultValue(),
            'priority'    => 10,
            'choices'     => $this->getHeroSearchPositionOptions(),
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
        ];
    }

    public function getSearchDisplayFieldAttributes(string $sectionID)
    {
        return [
            'type'        => 'multicheck',
            'settings'    => 'search_display',
            'label'       => esc_html__('Show search', 'municipio'),
            'section'     => $sectionID,
            'default'     => [],
            'priority'    => 10,
            'choices'     => $this->getSearchDisplayOptions(),
            'output' => [
                [
                    'type' => 'controller',
                    'as_object' => false,
                ]
            ],
        ];
    }

    public function getSearchFormShapeFieldAttributes(string $sectionID)
    {
        return [
            'type'        => 'select',
            'settings'    => 'search_form_shape',
            'label'       => esc_html__('Search form shape', 'municipio'),
            'section'     => $sectionID,
            'default'     => $this->getSearchFormShapeDefaultValue(),
            'priority'    => 10,
            'choices'     => $this->getSearchFormShapeOptions(),
            'output' => [
                [
                    'element'       => ['.search-form'],
                    'property'      => '--c-search-form-border-radius',
                    'units'         => 'px',
                ],
            ],
        ];
    }

    public function getHeroSearchPositionDefaultValue(): string
    {
        return 'centered';
    }

    public function getSearchFormShapeDefaultValue(): string
    {
        return '';
    }

    public function getHeroSearchPositionOptions():array {
        return [
            'top' => __('Top', 'municipio'),
            'centered' => __('Centered', 'municipio'),
            'bottom' => __('Bottom', 'municipio'),
        ];
    }

    public function getSearchDisplayOptions(): array
    {
        return [
            'hero' => __('Hero on frontpage', 'municipio'),
            'header_sub' => __('Header on sub pages', 'municipio'),
            'header' => __('Header on frontpage', 'municipio'),
            'mainmenu' => __('Option in main menu', 'municipio'),
            'mobile' => __('Option in mobile menu', 'municipio'),
            'hamburger_menu' => __('Hamburger menu', 'municipio'),
            'quicklinks' => __('Quicklinks menu', 'municipio'),
        ];
    }

    public function getSearchFormShapeOptions(): array
    {
        return [
            '' => __('Default', 'municipio'),
            '100' => __('Pill', 'municipio'),
        ];
    }
}
