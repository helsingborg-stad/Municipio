<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Helper\KirkiSwatches as KirkiSwatches;
use Municipio\Customizer\KirkiField;

class Drawer
{
    public const SECTION_ID = "municipio_customizer_section_drawer";

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'drawer_color_scheme',
            'label'       => esc_html__('Drawer color scheme', 'municipio'),
            'description' => esc_html__('Sets the color scheme for the items inside the drawers main area', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''          => esc_html__('Basic', 'municipio'),
                'primary'   => esc_html__('Primary', 'municipio'),
                'secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => [
                      'site.header.drawer'
                    ],
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'drawer_color_scheme_secondary_area',
            'label'       => esc_html__('Drawer secondary area', 'municipio'),
            'description' => esc_html__('If using both the areas in the drawer menu this will decide the bottom area', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'duotone-primary',
            'priority'    => 10,
            'choices'     => [
                ''                  => esc_html__('Basic', 'municipio'),
                'duotone-primary'   => esc_html__('Primary', 'municipio'),
                'duotone-secondary' => esc_html__('Secondary', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => [
                      'site.header.drawer'
                    ],
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'drawer_sublevel_trigger_icon',
            'label'       => esc_html__('Submenu expand icon', 'municipio'),
            'description' => esc_html__('The icon that will indicate that a submenu can be expanded', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'priority'    => 10,
            'choices'     => [
                ''        => esc_html__('Basic', 'municipio'),
                'caret'   => esc_html__('Caret', 'municipio'),
                'plus'    => esc_html__('Plus/Minus', 'municipio'),
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'sublevelTriggerIcon',
                    'context' => [
                    [
                        'context'  => 'site.mobile-menu',
                        'operator' => '==',
                    ],
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'switch',
            'settings'    => 'drawer_indent_sublevels',
            'label'       => esc_html__('Indent each level', 'municipio'),
            'description' => esc_html__('Submenus will indent one step for every level down', 'municipio'),
            'section'     => $sectionID,
            'default'     => false,
            'choices'   => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'priority'    => 10,
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => [
                      'site.header.drawer'
                    ],
                    'value_map' => [
                        true => 'indent-sublevels',
                    ]
                ]
            ],
        ]);
    }
}
