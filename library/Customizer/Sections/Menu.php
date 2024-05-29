<?php

namespace Municipio\Customizer\Sections;

use Kirki\Compatibility\Kirki;

class Menu
{
    private $defaultDrawerScreenSizes = ['xs', 'sm', 'md'];

    public function __construct(string $sectionID)
    {
        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'primary_menu_pagetree_fallback',
            'label'    => esc_html__('Use page tree as fallback for primary menu', 'municipio'),
            'section'  => $sectionID,
            'default'  => true,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'secondary_menu_pagetree_fallback',
            'label'    => esc_html__('Use page tree as fallback for secondary menu', 'municipio'),
            'section'  => $sectionID,
            'default'  => true,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'mobile_menu_pagetree_fallback',
            'label'    => esc_html__('Use page tree as fallback for mobile menu', 'municipio'),
            'section'  => $sectionID,
            'default'  => true,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'mega_menu_pagetree_fallback',
            'label'    => esc_html__('Use page tree as fallback for mega menu', 'municipio'),
            'section'  => $sectionID,
            'default'  => false,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'kirki'),
                false => esc_html__('Disabled', 'kirki'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, [
            'type'     => 'switch',
            'settings' => 'primary_menu_dropdown',
            'label'    => esc_html__('Show subitems as dropdown in main menu', 'municipio'),
            'section'  => $sectionID,
            'default'  => false,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ]
        ]);

        Kirki::add_field(\Municipio\Customizer::KIRKI_CONFIG, $this->getDrawerScreenSizesFieldArguments($sectionID));
    }

    public function getDrawerScreenSizesFieldArguments(string $sectionID)
    {
        return [
            'type'        => 'multicheck',
            'settings'    => 'drawer_screen_sizes',
            'label'       => esc_html__('Drawer screen sizes', 'municipio'),
            'description' => esc_html__('Select which screen sizes the drawer menu should be visible on.', 'municipio'),
            'section'     => $sectionID,
            'default'     => $this->getDefaultDrawerScreenSizes(),
            'priority'    => 10,
            'choices'     => $this->getDrawerScreenSizeOptions(),
            'default'     => $this->getDefaultDrawerScreenSizes(),
            'output'      => [
                ['type' => 'controller']
            ]
        ];
    }

    public function getDrawerScreenSizeOptions()
    {
        return [
            'xs' => esc_html__('Extra small', 'municipio'),
            'sm' => esc_html__('Small', 'municipio'),
            'md' => esc_html__('Medium', 'municipio'),
            'lg' => esc_html__('Large', 'municipio'),
            'xl' => esc_html__('Extra large', 'municipio'),
        ];
    }

    public function getDefaultDrawerScreenSizes()
    {
        return $this->defaultDrawerScreenSizes;
    }
}
