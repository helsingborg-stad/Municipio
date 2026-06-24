<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Helper\ColorSwatches as ColorSwatches;
use Municipio\Customizer\CustomizerField;

class Breadcrumbs
{
    public const SECTION_ID = "municipio_customizer_section_breadcrumbs";

    public function __construct(string $sectionID)
    {
        CustomizerField::addField([
            'type'        => 'slider',
            'settings'    => 'breadcrumb_truncate',
            'label'       => esc_html__('Truncate amount of letters', 'municipio'),
            'description' => esc_html__('The actual text will still be shown in a tooltip when hovered.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 30,
            'choices'     => [
                'min'  => 0,
                'max'  => 50,
                'step' => 1,
            ],
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'defaultTruncate',
                    'context' => [
                        [
                            'context'  => 'component.breadcrumb',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);

        CustomizerField::addField([
            'type'        => 'switch',
            'settings'    => 'breadcrumb_show_home_icon',
            'label'    => esc_html__('Show home icon', 'municipio'),
            'section'  => $sectionID,
            'default'  => true,
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
            'type'        => 'switch',
            'settings'    => 'breadcrumb_show_prefix_label',
            'label'    => esc_html__('Show prefix usually used for screen readers only (eg. you are here)', 'municipio'),
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
    }
}
