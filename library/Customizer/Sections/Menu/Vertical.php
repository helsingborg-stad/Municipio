<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Customizer\KirkiField;

class Vertical
{
    public const SECTION_ID = "municipio_customizer_section_vertical";

    public function __construct(string $sectionID)
    {
    
        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'vertical_menu_sublevel_trigger_icon',
            'label'       => esc_html__('Submenu expand icon', 'municipio'),
            'description' => esc_html__('The icon that will indicate that a submenu can be expanded', 'municipio'),
            'section'     => $sectionID,
            'default'     => 'expand_more',
            'priority'    => 10,
            'choices'     => [
                'expand_more' => esc_html__('Directional Caret', 'municipio'), //Standard material icon class
                'toggleAriaPressedPlusMinus' => esc_html__('Plus/Minus', 'municipio'), //Custom svg
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'expandIcon',
                    'context' => [
                        [
                            'context'  => 'component.nav',
                            'operator' => '==',
                        ]
                    ]
                ]
            ],
        ]);

        KirkiField::addField([
            'type'        => 'select',
            'settings'    => 'vetical_menu_indent_sublevels',
            'label'       => esc_html__('Indent each level', 'municipio'),
            'description' => esc_html__('Submenus will indent one step for every level down', 'municipio'),
            'section'     => $sectionID,
            'default'     => '',
            'choices'   => [
                'indent-sublevels'  => esc_html__('Enabled', 'municipio'),
                '' => esc_html__('Disabled', 'municipio'),
            ],
            'priority'    => 10,
            'output'      => [
                [
                    'type'    => 'modifier',
                    'context' => [
                        [
                            'context'  => 'component.nav',
                            'operator' => '==',
                        ]
                    ]
                ]
            ],
        ]);
    }
}
