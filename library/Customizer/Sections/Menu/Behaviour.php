<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\CustomizerField;

class Behaviour
{
    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type' => 'multicheck',
            'settings' => 'menu_pagetree_fallback_menus',
            'label' => esc_html__('Use page tree as fallback for menus', 'municipio'),
            'description' => esc_html__('Choose which menus should use the page tree when no assigned menu exists.', 'municipio'),
            'section' => $sectionID,
            'default' => ['primary', 'secondary', 'mobile'],
            'priority' => 10,
            'choices' => [
                'primary' => esc_html__('Primary menu', 'municipio'),
                'secondary' => esc_html__('Secondary menu', 'municipio'),
                'mobile' => esc_html__('Mobile menu', 'municipio'),
                'mega' => esc_html__('Mega menu', 'municipio'),
            ],
            'layout' => 'horizontal',
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'primary_menu_dropdown',
            'label' => esc_html__('Show subitems as dropdown in main menu', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'switch',
            'settings' => 'primary_menu_dropdown_extended',
            'label' => esc_html__('Extends the dropdown behavior making it more complete', 'municipio'),
            'section' => $sectionID,
            'default' => false,
            'priority' => 10,
            'choices' => [
                true => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'active_callback' => [
                [
                    'setting' => 'primary_menu_dropdown',
                    'operator' => '==',
                    'value' => true,
                ],
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

        CustomizerField::addField([
            'type' => 'radio',
            'settings' => 'secondary_navigation_position',
            'label' => esc_html__('Secondary navigation position', 'municipio'),
            'section' => $sectionID,
            'default' => 'left',
            'priority' => 10,
            'choices' => [
                'left' => esc_html__('Left', 'municipio'),
                'right' => esc_html__('Right', 'municipio'),
                'hidden' => esc_html__('Hidden', 'municipio'),
            ],
            'output' => [
                ['type' => 'controller'],
            ],
        ]);

    }
}
