<?php

namespace Municipio\Customizer\Sections;

use Municipio\Customizer\KirkiField;

class Icons
{
    public function __construct($sectionID)
    {
        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'icon_style',
            'label'    => esc_html__('Style', 'municipio'),
            'section'  => $sectionID,
            'default'  => 'outlined',
            'priority' => 10,
            'choices'  => [
                'outlined'   => esc_html__('Outlined', 'municipio'),
                'rounded' => esc_html__('Rounded', 'municipio'),
                'sharp' => esc_html__('Sharp', 'municipio'),
            ],
            'description' => esc_html__( 'Set the default aperance of the icons to match you websites style.', 'kirki' ),
        ]);

        KirkiField::addField([
            'type'     => 'select',
            'settings' => 'icon_weight',
            'label'    => esc_html__('Weight', 'municipio'),
            'section'  => $sectionID,
            'default'  => '400',
            'priority' => 20,
            'choices'  => [
                '200'   => esc_html__('Light', 'municipio'),
                '400' => esc_html__('Medium', 'municipio'),
                '600' => esc_html__('Bold', 'municipio'),
            ],
            'description' => esc_html__( 'Set the boldness of the icons.', 'kirki' ),
            'output' => [
                [
                    'element' => ':root',
                    'property' => '--current-material-symbols-weight',
                ],
            ],
        ]);

        KirkiField::addField([
            'type'     => 'switch',
            'settings' => 'icon_filled',
            'label'    => esc_html__('Filled icons', 'municipio'),
            'section'  => $sectionID,
            'default'  => false,
            'priority' => 30,
            'choices'  => [
                true  => esc_html__('Filled', 'kirki'),
                false => esc_html__('Outlined', 'kirki'),
            ],
            'description' => esc_html__('Determines if icons should be filled as a default, or not. If the icon component has any value set, that will be used instead. This settly do not apply to all icons, only those with support.', 'kirki' ),
            'output'          => [
                [
                    'type'    => 'component_data',
                    'dataKey' => 'defaultFilled',
                    'context' => [
                        [
                            'context'  => 'component.icon',
                            'operator' => '=='
                        ],
                    ],
                ],
            ],
        ]);
    }
}
