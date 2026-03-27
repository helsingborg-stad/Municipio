<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Borders
{
    public const SECTION_ID = "municipio_customizer_section_border";

    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'border_width_outline',
            'label'     => esc_html__('Outline', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 1,
            'choices'   => [
                'min'  => 0,
                'max'  => 8,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--border-width-outline',
                    'units'    => 'px'
                ]
            ],
        ]);
    }
}
