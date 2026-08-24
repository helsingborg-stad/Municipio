<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Helper\ColorSwatches as ColorSwatches;
use Municipio\Customizer\CustomizerField;

class Drawer
{
    public const SECTION_ID = "municipio_customizer_section_drawer";

    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
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

        CustomizerField::addField([
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

        CustomizerField::addField([
            'type'     => 'switch',
            'settings' => 'drawer_divider',
            'label'    => esc_html__('Drawer divider', 'municipio'),
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

        CustomizerField::addField([
            'type'     => 'switch',
            'settings' => 'drawer_divider_top_level_only',
            'label'    => esc_html__('Drawer divider (top level only)', 'municipio'),
            'section'  => $sectionID,
            'default'  => true,
            'priority' => 10,
            'choices'  => [
                true  => esc_html__('Enabled', 'municipio'),
                false => esc_html__('Disabled', 'municipio'),
            ],
            'output'   => [
                ['type' => 'controller']
            ],
            'active_callback' => [
                [
                    'setting'  => 'drawer_divider',
                    'operator' => '==',
                    'value'    => true,
                ],
            ],
        ]);
    }
}
