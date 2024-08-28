<?php

namespace Municipio\Customizer\Sections\Menu;

use Municipio\Helper\KirkiSwatches as KirkiSwatches;
use Municipio\Customizer\KirkiField;

class Breadcrumbs
{
    public const SECTION_ID = "municipio_customizer_section_breadcrumbs";

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'        => 'slider',
            'settings'    => 'breadcrumb_truncate',
            'label'       => esc_html__('Truncate amount of letters', 'municipio'),
            'description' => esc_html__('The actual text will still be shown in a tooltip when hovered.', 'municipio'),
            'section'     => $sectionID,
            'default'     => 20,
            'choices'     => [
                'min'  => 0,
                'max'  => 30,
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
    }
}
