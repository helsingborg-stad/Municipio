<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Radius
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField([
            'type'      => 'slider',
            'settings'  => 'radius_md',
            'label'     => esc_html__('Radius', 'municipio'),
            'section'   => $sectionID,
            'transport' => 'auto',
            'default'   => 8,
            'choices'   => [
                'min'  => 0,
                'max'  => 12,
                'step' => 1,
            ],
            'output'    => [
                [
                    'element'  => ':root',
                    'property' => '--radius-md',
                    'units'    => 'px'
                ]
            ],
        ]);
    }
}
