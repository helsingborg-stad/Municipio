<?php

namespace Municipio\Customizer\Sections\Component;

use Municipio\Customizer\KirkiField;

class Button
{
    public function __construct(string $sectionID)
    {
        KirkiField::addField($this->getShapeFieldAttributes($sectionID));
    }

    public function getShapeFieldAttributes(string $sectionID)
    {
        return [
            'type'        => 'select',
            'settings'    => 'button_shape',
            'label'       => esc_html__('Button shape', 'municipio'),
            'description' => esc_html__('Choose the shape of the buttons.', 'municipio'),
            'choices'     => $this->getShapeOptions(),
            'default'     => $this->getShapeDefaultValue(),
            'section'     => $sectionID,
            'output'      => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'shape',
                    'context' => [
                        [
                            'context'  => 'component.button',
                            'operator' => '=='
                        ],
                        [
                            'context'  => 'component.megamenu.button.child',
                            'operator' => '!=='
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getShapeDefaultValue()
    {
        return 'default';
    }

    public function getShapeOptions()
    {
        return [
            'default' => esc_html__('Standard', 'municipio'),
            'pill'    => esc_html__('Pill', 'municipio')
        ];
    }
}
